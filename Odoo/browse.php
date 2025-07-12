<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Skill.php';
require_once 'classes/SwapRequest.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$skill = new Skill($db);
$swapRequest = new SwapRequest($db);

// Handle search
$search_results = [];
$search_skill = $_GET['skill'] ?? '';
$search_location = $_GET['location'] ?? '';

if ($search_skill || $search_location) {
    $search_results = $user->searchUsers($search_skill, $search_location);
    // Remove current user from results
    $search_results = array_filter($search_results, function($u) {
        return $u['id'] != getCurrentUserId();
    });
} else {
    // Show all public users by default
    $search_results = $user->searchUsers();
    $search_results = array_filter($search_results, function($u) {
        return $u['id'] != getCurrentUserId();
    });
}

$all_skills = $skill->getAllSkills();

// Handle swap request
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'send_request') {
    $requested_user_id = $_POST['requested_user_id'];
    $requester_skill_id = $_POST['requester_skill_id'];
    $requested_skill_id = $_POST['requested_skill_id'];
    $message = $_POST['message'];
    
    $success = $swapRequest->createRequest(getCurrentUserId(), $requested_user_id, $requester_skill_id, $requested_skill_id, $message);
    if ($success) {
        $success_message = "Swap request sent successfully!";
    } else {
        $error_message = "Failed to send swap request.";
    }
}

$current_user_skills = $skill->getUserSkillsOffered(getCurrentUserId());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Users - SkillSwap</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><a href="index.php" style="text-decoration: none; color: inherit;">SkillSwap</a></h2>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Home</a>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php" class="nav-link">Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div style="padding: 40px 0;">
                <h1 style="text-align: center; margin-bottom: 40px;">Browse Users</h1>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                
                <!-- Search Form -->
                <form class="search-form" method="GET" style="margin-bottom: 40px;">
                    <div class="search-inputs">
                        <input type="text" name="skill" placeholder="Search by skill" 
                               value="<?php echo htmlspecialchars($search_skill); ?>">
                        <input type="text" name="location" placeholder="Location" 
                               value="<?php echo htmlspecialchars($search_location); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="browse.php" class="btn btn-secondary">Clear</a>
                    </div>
                </form>

                <!-- Results -->
                <?php if (!empty($search_results)): ?>
                    <div class="search-results">
                        <h3>Users Found (<?php echo count($search_results); ?>)</h3>
                        <div class="user-grid">
                            <?php foreach ($search_results as $result_user): ?>
                                <div class="user-card">
                                    <div class="user-info">
                                        <h4><?php echo htmlspecialchars($result_user['full_name']); ?></h4>
                                        <p class="username">@<?php echo htmlspecialchars($result_user['username']); ?></p>
                                        <?php if ($result_user['location']): ?>
                                            <p class="location"><?php echo htmlspecialchars($result_user['location']); ?></p>
                                        <?php endif; ?>
                                        <?php if ($result_user['availability']): ?>
                                            <p style="font-size: 12px; color: #666;">
                                                Available: <?php echo htmlspecialchars($result_user['availability']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- User Skills -->
                                    <?php 
                                    $user_skills_offered = $skill->getUserSkillsOffered($result_user['id']);
                                    $user_skills_wanted = $skill->getUserSkillsWanted($result_user['id']);
                                    ?>
                                    
                                    <?php if (!empty($user_skills_offered)): ?>
                                        <div style="margin: 15px 0;">
                                            <strong style="font-size: 12px; color: #666;">Offers:</strong>
                                            <div style="margin-top: 5px;">
                                                <?php foreach (array_slice($user_skills_offered, 0, 3) as $skill_item): ?>
                                                    <span class="skill-tag" style="font-size: 11px; padding: 3px 8px; margin: 2px;">
                                                        <?php echo htmlspecialchars($skill_item['name']); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                                <?php if (count($user_skills_offered) > 3): ?>
                                                    <span style="font-size: 11px; color: #666;">+<?php echo count($user_skills_offered) - 3; ?> more</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div style="margin-top: 15px;">
                                        <a href="user-profile.php?id=<?php echo $result_user['id']; ?>" class="btn btn-small">View Profile</a>
                                        <?php if (!empty($current_user_skills) && !empty($user_skills_offered)): ?>
                                            <button onclick="openSwapModal(<?php echo $result_user['id']; ?>, '<?php echo htmlspecialchars($result_user['full_name']); ?>')" 
                                                    class="btn btn-small btn-primary">Request Swap</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <p>No users found. Try adjusting your search criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Swap Request Modal -->
    <div id="swapModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeSwapModal()">&times;</span>
            <h3>Send Swap Request</h3>
            <form method="POST">
                <input type="hidden" name="action" value="send_request">
                <input type="hidden" name="requested_user_id" id="requested_user_id">
                
                <div class="form-group">
                    <label>Requesting swap with: <span id="requested_user_name"></span></label>
                </div>
                
                <div class="form-group">
                    <label>Your Skill to Offer</label>
                    <select name="requester_skill_id" required>
                        <option value="">Select your skill</option>
                        <?php foreach ($current_user_skills as $my_skill): ?>
                            <option value="<?php echo $my_skill['id']; ?>">
                                <?php echo htmlspecialchars($my_skill['name']); ?> (<?php echo $my_skill['proficiency_level']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Skill You Want</label>
                    <select name="requested_skill_id" id="requested_skill_select" required>
                        <option value="">Select skill you want to learn</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message (Optional)</label>
                    <textarea name="message" placeholder="Introduce yourself and explain what you'd like to learn..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Request</button>
                <button type="button" onclick="closeSwapModal()" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        let userSkills = {};
        
        // Store user skills data
        <?php foreach ($search_results as $result_user): ?>
            <?php $user_skills_offered = $skill->getUserSkillsOffered($result_user['id']); ?>
            userSkills[<?php echo $result_user['id']; ?>] = <?php echo json_encode($user_skills_offered); ?>;
        <?php endforeach; ?>
        
        function openSwapModal(userId, userName) {
            document.getElementById('requested_user_id').value = userId;
            document.getElementById('requested_user_name').textContent = userName;
            
            // Populate skills dropdown
            const skillSelect = document.getElementById('requested_skill_select');
            skillSelect.innerHTML = '<option value="">Select skill you want to learn</option>';
            
            if (userSkills[userId]) {
                userSkills[userId].forEach(skill => {
                    const option = document.createElement('option');
                    option.value = skill.id;
                    option.textContent = skill.name + ' (' + skill.proficiency_level + ')';
                    skillSelect.appendChild(option);
                });
            }
            
            document.getElementById('swapModal').style.display = 'block';
        }
        
        function closeSwapModal() {
            document.getElementById('swapModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('swapModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    
    <script>
        // Show success popup
        function showSuccessPopup(message) {
            // Create popup element
            const popup = document.createElement('div');
            popup.className = 'success-popup';
            popup.innerHTML = `
                <div class="popup-content">
                    <div class="popup-icon">✅</div>
                    <h3>Success!</h3>
                    <p>${message}</p>
                    <button onclick="closeSuccessPopup()" class="btn btn-primary">OK</button>
                </div>
            `;
            
            document.body.appendChild(popup);
            
            // Auto-close after 5 seconds
            setTimeout(() => {
                closeSuccessPopup();
            }, 5000);
        }

        function closeSuccessPopup() {
            const popup = document.querySelector('.success-popup');
            if (popup) {
                popup.remove();
            }
        }

        // Show popup if there's a success message
        <?php if (isset($success_message)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessPopup('<?php echo addslashes($success_message); ?>');
            });
        <?php endif; ?>
    </script>
    
    <style>
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #000;
        }
    </style>

    <style>
        .success-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            animation: fadeIn 0.3s ease;
        }

        .popup-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        .popup-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .popup-content h3 {
            color: #28a745;
            margin-bottom: 15px;
        }

        .popup-content p {
            margin-bottom: 25px;
            color: #666;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</body>
</html>
