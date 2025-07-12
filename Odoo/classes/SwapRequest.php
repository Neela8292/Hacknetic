<?php
require_once 'config/database.php';
require_once 'config/email.php'; // Include the email configuration

// PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Autoload PHPMailer classes (if not using Composer)
// Adjust path if your PHPMailer setup is different
require_once 'classes/PHPMailer/src/Exception.php';
require_once 'classes/PHPMailer/src/PHPMailer.php';
require_once 'classes/PHPMailer/src/SMTP.php';

class SwapRequest {
    private $conn;
    private $table_name = "swap_requests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createRequest($requester_id, $requested_user_id, $requester_skill_id, $requested_skill_id, $message = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (requester_id, requested_user_id, requester_skill_id, requested_skill_id, message) 
                  VALUES (:requester_id, :requested_user_id, :requester_skill_id, :requested_skill_id, :message)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':requester_id', $requester_id);
        $stmt->bindParam(':requested_user_id', $requested_user_id);
        $stmt->bindParam(':requester_skill_id', $requester_skill_id);
        $stmt->bindParam(':requested_skill_id', $requested_skill_id);
        $stmt->bindParam(':message', $message);
        
        if ($stmt->execute()) {
            $new_request_id = $this->conn->lastInsertId();
            // Attempt to send email, but don't block the request creation if email fails
            if (!$this->sendRequestNotificationEmail($new_request_id)) {
                // Log error or handle it as needed, e.g., set a flag in the DB
                error_log("Failed to send email for swap request ID: " . $new_request_id);
            }
            return true;
        }
        return false;
    }

    public function getUserRequests($user_id) {
        $query = "SELECT sr.*, 
                         u1.username as requester_username, u1.full_name as requester_name,
                         u2.username as requested_username, u2.full_name as requested_name,
                         s1.name as requester_skill_name,
                         s2.name as requested_skill_name
                  FROM " . $this->table_name . " sr
                  JOIN users u1 ON sr.requester_id = u1.id
                  JOIN users u2 ON sr.requested_user_id = u2.id
                  JOIN skills s1 ON sr.requester_skill_id = s1.id
                  JOIN skills s2 ON sr.requested_skill_id = s2.id
                  WHERE sr.requester_id = :user_id OR sr.requested_user_id = :user_id
                  ORDER BY sr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRequestStatus($request_id, $status, $user_id) {
        // Verify user has permission to update this request
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :request_id AND (requester_id = :user_id OR requested_user_id = :user_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    public function deleteRequest($request_id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :request_id AND requester_id = :user_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    public function sendRequestNotificationEmail($requestId) {
        $query = "SELECT sr.*, 
                         u1.email as requester_email, u1.full_name as requester_name,
                         u2.email as requested_email, u2.full_name as requested_name,
                         s1.name as requester_skill_name,
                         s2.name as requested_skill_name
                  FROM " . $this->table_name . " sr
                  JOIN users u1 ON sr.requester_id = u1.id
                  JOIN users u2 ON sr.requested_user_id = u2.id
                  JOIN skills s1 ON sr.requester_skill_id = s1.id
                  JOIN skills s2 ON sr.requested_skill_id = s2.id
                  WHERE sr.id = :request_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $requestId);
        $stmt->execute();
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            $mail = new PHPMailer(true); // Passing `true` enables exceptions
            try {
                // Server settings
                $mail->SMTPDebug = SMTP::DEBUG_OFF; // Set to SMTP::DEBUG_SERVER for detailed debug output
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port       = SMTP_PORT;

                // Recipients
                $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
                $mail->addAddress($request['requested_email'], $request['requested_name']); // Add a recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = "New Skill Swap Request from " . $request['requester_name'];
                
                $accept_link = "http://localhost/dashboard.php?action=update_request_status&request_id=" . $request['id'] . "&status=accepted";
                $reject_link = "http://localhost/dashboard.php?action=update_request_status&request_id=" . $request['id'] . "&status=rejected";
                
                $body = "
                    <html>
                    <head>
                        <title>New Skill Swap Request</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
                            h2 { color: #007bff; }
                            .button { display: inline-block; padding: 10px 20px; margin: 10px 5px; border-radius: 5px; text-decoration: none; color: white; }
                            .accept { background-color: #28a745; }
                            .reject { background-color: #dc3545; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Hello " . htmlspecialchars($request['requested_name']) . ",</h2>
                            <p>You have received a new skill swap request from <strong>" . htmlspecialchars($request['requester_name']) . "</strong>.</p>
                            <p><strong>Skills involved:</strong></p>
                            <ul>
                                <li><strong>" . htmlspecialchars($request['requester_name']) . " offers:</strong> " . htmlspecialchars($request['requester_skill_name']) . "</li>
                                <li><strong>" . htmlspecialchars($request['requested_name']) . " wants:</strong> " . htmlspecialchars($request['requested_skill_name']) . "</li>
                            </ul>
                            ";
                if (!empty($request['message'])) {
                    $body .= "<p><strong>Message:</strong> " . nl2br(htmlspecialchars($request['message'])) . "</p>";
                }
                $body .= "
                            <p>Please review this request on your dashboard or by clicking the links below:</p>
                            <p>
                                <a href='" . $accept_link . "' class='button accept'>Accept Request</a>
                                <a href='" . $reject_link . "' class='button reject'>Reject Request</a>
                            </p>
                            <p>Thank you,<br>The SkillSwap Team</p>
                        </div>
                    </body>
                    </html>
                ";
                $mail->Body = $body;
                $mail->AltBody = 'Hello ' . htmlspecialchars($request['requested_name']) . ', You have received a new skill swap request from ' . htmlspecialchars($request['requester_name']) . '. Skills involved: ' . htmlspecialchars($request['requester_name']) . ' offers: ' . htmlspecialchars($request['requester_skill_name']) . ' and ' . htmlspecialchars($request['requested_name']) . ' wants: ' . htmlspecialchars($request['requested_skill_name']) . '. Message: ' . htmlspecialchars($request['message']) . '. Please visit your dashboard to respond.';

                $mail->send();
                return true;
            } catch (Exception $e) {
                // You can log the error here for debugging
                error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                return false;
            }
        }
        return false;
    }
}
?>
