<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$current_user = $user->getUserById(getCurrentUserId());
$user_skills_offered = $skill->getUserSkillsOffered(getCurrentUserId());
$user_skills_wanted = $skill->getUserSkillsWanted(getCurrentUserId());
$user_requests = $swapRequest->getUserRequests(getCurrentUserId());

// Handle skill management
$message = '';
$message_type = '';

if ($_POST) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_skill_offered':
                    $skill_id = $_POST['skill_id'];
                    $proficiency_level = $_POST['proficiency_level'];
                    $description = $_POST['description'] ?? '';
                    if ($skill->addUserSkillOffered(getCurrentUserId(), $skill_id, $proficiency_level, $description)) {
                        $message = 'Skill added successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to add skill.';
                        $message_type = 'error';
                    }
                    break;
                    
                case 'add_skill_wanted':
                    $skill_id = $_POST['skill_id'];
                    $desired_level = $_POST['desired_level'];
                    $description = $_POST['description'] ?? '';
                    if ($skill->addUserSkillWanted(getCurrentUserId(), $skill_id, $desired_level, $description)) {
                        $message = 'Skill added successfully!';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to add skill.';
                        $message_type = 'error';
                    }
                    break;
                    
                case 'remove_skill_offered':
                    $skill_id = $_POST['skill_id'];
                    if ($skill->removeUserSkillOffered(getCurrentUserId(), $skill_id)) {
                        $message = 'Skill removed successfully!';
                        $message_type = 'success';
                    }
                    break;
                    
                case 'remove_skill_wanted':
                    $skill_id = $_POST['skill_id'];
                    if ($skill->removeUserSkillWanted(getCurrentUserId(), $skill_id)) {
                        $message = 'Skill removed successfully!';
                        $message_type = 'success';
                    }
                    break;
                    
                case 'update_request_status':
                    $request_id = $_POST['request_id'];
                    $status = $_POST['status'];
                    if ($swapRequest->updateRequestStatus($request_id, $status, getCurrentUserId())) {
                        $message = 'Request updated successfully!';
                        $message_type = 'success';
                    }
                    break;
                    
                case 'delete_request':
                    $request_id = $_POST['request_id'];
                    if ($swapRequest->deleteRequest($request_id, getCurrentUserId())) {
                        $message = 'Request deleted successfully!';
                        $message_type = 'success';
                    }
                    break;
            }
        }
        
        // Refresh data after any action
        $current_user = $user->getUserById(getCurrentUserId());
        $user_skills_offered = $skill->getUserSkillsOffered(getCurrentUserId());
        $user_skills_wanted = $skill->getUserSkillsWanted(getCurrentUserId());
        $user_requests = $swapRequest->getUserRequests(getCurrentUserId());
        
    } catch (Exception $e) {
        $message = 'An error occurred: ' . $e->getMessage();
        $message_type = 'error';
    }
}

$all_skills = $skill->getAllSkills();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SkillSwap</title>
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
                <a href="browse.php" class="nav-link">Browse</a>
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
            <div class="dashboard">
                <div class="dashboard-header">
                    <h1>Welcome back, <?php echo htmlspecialchars($current_user['full_name']); ?>!</h1>
                    <p>Manage your skills and swap requests</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>" style="margin-bottom: 30px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="dashboard-grid">
                    <!-- Skills Offered -->
                    <div class="dashboard-card">
                        <h3>Skills I Offer</h3>
                        <?php if (!empty($user_skills_offered)): ?>
                            <ul class="skills-list">
                                <?php foreach ($user_skills_offered as $skill_item): ?>
                                    <li>
                                        <div class="skill-info">
                                            <div class="skill-name"><?php echo htmlspecialchars($skill_item['name']); ?></div>
                                            <div class="skill-level"><?php echo htmlspecialchars($skill_item['proficiency_level']); ?></div>
                                            <?php if ($skill_item['description']): ?>
                                                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                                                    <?php echo htmlspecialchars($skill_item['description']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="skill-actions">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="remove_skill_offered">
                                                <input type="hidden" name="skill_id" value="<?php echo $skill_item['id']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger" 
                                                        onclick="return confirm('Remove this skill?')">Remove</button>
                                            </form>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No skills added yet.</p>
                        <?php endif; ?>
                        
                        <button type="button" onclick="toggleForm('add-skill-offered')" class="btn btn-primary" style="margin-top: 20px;">
                            Add Skill
                        </button>
                        
                        <div id="add-skill-offered" class="form-container" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa;">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_skill_offered">
                                <div class="form-group">
                                    <label>Skill</label>
                                    <select name="skill_id" required>
                                        <option value="">Select a skill</option>
                                        <?php foreach ($all_skills as $skill_option): ?>
                                            <option value="<?php echo $skill_option['id']; ?>">
                                                <?php echo htmlspecialchars($skill_option['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Proficiency Level</label>
                                    <select name="proficiency_level" required>
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate" selected>Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Description (Optional)</label>
                                    <textarea name="description" placeholder="Describe your experience with this skill"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Skill</button>
                                <button type="button" onclick="toggleForm('add-skill-offered')" class="btn btn-secondary">Cancel</button>
                            </form>
                        </div>
                    </div>

                    <!-- Skills Wanted -->
                    <div class="dashboard-card">
                        <h3>Skills I Want to Learn</h3>
                        <?php if (!empty($user_skills_wanted)): ?>
                            <ul class="skills-list">
                                <?php foreach ($user_skills_wanted as $skill_item): ?>
                                    <li>
                                        <div class="skill-info">
                                            <div class="skill-name"><?php echo htmlspecialchars($skill_item['name']); ?></div>
                                            <div class="skill-level">Desired: <?php echo htmlspecialchars($skill_item['desired_level']); ?></div>
                                            <?php if ($skill_item['description']): ?>
                                                <div style="font-size: 12px; color: #888; margin-top: 5px;">
                                                    <?php echo htmlspecialchars($skill_item['description']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="skill-actions">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="remove_skill_wanted">
                                                <input type="hidden" name="skill_id" value="<?php echo $skill_item['id']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger" 
                                                        onclick="return confirm('Remove this skill?')">Remove</button>
                                            </form>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No skills added yet.</p>
                        <?php endif; ?>
                        
                        <button type="button" onclick="toggleForm('add-skill-wanted')" class="btn btn-primary" style="margin-top: 20px;">
                            Add Skill
                        </button>
                        
                        <div id="add-skill-wanted" class="form-container" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa;">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_skill_wanted">
                                <div class="form-group">
                                    <label>Skill</label>
                                    <select name="skill_id" required>
                                        <option value="">Select a skill</option>
                                        <?php foreach ($all_skills as $skill_option): ?>
                                            <option value="<?php echo $skill_option['id']; ?>">
                                                <?php echo htmlspecialchars($skill_option['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Desired Level</label>
                                    <select name="desired_level" required>
                                        <option value="Beginner" selected>Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Description (Optional)</label>
                                    <textarea name="description" placeholder="What would you like to learn about this skill?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Skill</button>
                                <button type="button" onclick="toggleForm('add-skill-wanted')" class="btn btn-secondary">Cancel</button>
                            </form>
                        </div>
                    </div>

                    <!-- Swap Requests -->
                    <div class="dashboard-card" style="grid-column: 1 / -1;">
                        <h3>My Swap Requests</h3>
                        <?php if (!empty($user_requests)): ?>
                            <?php foreach ($user_requests as $request): ?>
                                <div class="request-card">
                                    <div class="request-header">
                                        <div>
                                            <strong>
                                                <?php if ($request['requester_id'] == getCurrentUserId()): ?>
                                                    Request to <?php echo htmlspecialchars($request['requested_name']); ?>
                                                <?php else: ?>
                                                    Request from <?php echo htmlspecialchars($request['requester_name']); ?>
                                                <?php endif; ?>
                                            </strong>
                                        </div>
                                        <span class="request-status status-<?php echo $request['status']; ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="request-details">
                                        <p>
                                            <strong>Swap:</strong> 
                                            <?php echo htmlspecialchars($request['requester_skill_name']); ?> 
                                            ↔ 
                                            <?php echo htmlspecialchars($request['requested_skill_name']); ?>
                                        </p>
                                        <?php if ($request['message']): ?>
                                            <p><strong>Message:</strong> <?php echo htmlspecialchars($request['message']); ?></p>
                                        <?php endif; ?>
                                        <p><small>Created: <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></small></p>
                                    </div>
                                    
                                    <div class="request-actions">
                                        <?php if ($request['requested_user_id'] == getCurrentUserId() && $request['status'] == 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name" style="display: inline;">
                                                <input type="hidden" name="action" value="update_request_status">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="btn btn-small btn-success">Accept</button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_request_status">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-small btn-danger">Reject</button>
                                            </form>
                                        <?php elseif ($request['requester_id'] == getCurrentUserId() && $request['status'] == 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_request">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-small btn-danger" 
                                                        onclick="return confirm('Delete this request?')">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No swap requests yet. <a href="browse.php">Browse users</a> to start swapping skills!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
