<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Skill.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$skill = new Skill($db);

$current_user = $user->getUserById(getCurrentUserId());
$user_skills_offered = $skill->getUserSkillsOffered(getCurrentUserId());
$user_skills_wanted = $skill->getUserSkillsWanted(getCurrentUserId());
$all_skills = $skill->getAllSkills();

$success = '';
$error = '';

if ($_POST) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_profile':
                    $full_name = trim($_POST['full_name']);
                    $location = trim($_POST['location']);
                    $availability = trim($_POST['availability']);
                    $is_public = isset($_POST['is_public']) ? 1 : 0;
                    
                    if (empty($full_name)) {
                        $error = 'Full name is required.';
                    } else {
                        $data = [
                            'full_name' => $full_name,
                            'location' => $location,
                            'availability' => $availability,
                            'is_public' => $is_public
                        ];
                        
                        if ($user->updateProfile(getCurrentUserId(), $data)) {
                            $success = 'Profile updated successfully!';
                            $current_user = $user->getUserById(getCurrentUserId());
                        } else {
                            $error = 'Failed to update profile.';
                        }
                    }
                    break;
                    
                case 'add_skill_offered':
                    $skill_id = $_POST['skill_id'];
                    $proficiency_level = $_POST['proficiency_level'];
                    $description = $_POST['description'] ?? '';
                    if ($skill->addUserSkillOffered(getCurrentUserId(), $skill_id, $proficiency_level, $description)) {
                        $success = 'Skill added successfully!';
                        $user_skills_offered = $skill->getUserSkillsOffered(getCurrentUserId());
                    } else {
                        $error = 'Failed to add skill or skill already exists.';
                    }
                    break;
                    
                case 'add_skill_wanted':
                    $skill_id = $_POST['skill_id'];
                    $desired_level = $_POST['desired_level'];
                    $description = $_POST['description'] ?? '';
                    if ($skill->addUserSkillWanted(getCurrentUserId(), $skill_id, $desired_level, $description)) {
                        $success = 'Skill added successfully!';
                        $user_skills_wanted = $skill->getUserSkillsWanted(getCurrentUserId());
                    } else {
                        $error = 'Failed to add skill or skill already exists.';
                    }
                    break;
                    
                case 'remove_skill_offered':
                    $skill_id = $_POST['skill_id'];
                    if ($skill->removeUserSkillOffered(getCurrentUserId(), $skill_id)) {
                        $success = 'Skill removed successfully!';
                        $user_skills_offered = $skill->getUserSkillsOffered(getCurrentUserId());
                    }
                    break;
                    
                case 'remove_skill_wanted':
                    $skill_id = $_POST['skill_id'];
                    if ($skill->removeUserSkillWanted(getCurrentUserId(), $skill_id)) {
                        $success = 'Skill removed successfully!';
                        $user_skills_wanted = $skill->getUserSkillsWanted(getCurrentUserId());
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $error = 'An error occurred: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SkillSwap</title>
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
                <a href="browse.php" class="nav-link">Browse</a>
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
                <h1 style="text-align: center; margin-bottom: 40px;">My Profile</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="dashboard-grid">
                    <!-- Profile Information -->
                    <div class="dashboard-card">
                        <h3>Profile Information</h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="form-group">
                                <label for="full_name">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" required 
                                       value="<?php echo htmlspecialchars($current_user['full_name']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($current_user['location'] ?? ''); ?>"
                                       placeholder="e.g., New York, NY">
                            </div>
                            
                            <div class="form-group">
                                <label for="availability">Availability</label>
                                <input type="text" id="availability" name="availability" 
                                       value="<?php echo htmlspecialchars($current_user['availability'] ?? ''); ?>"
                                       placeholder="e.g., Weekends, Evenings">
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_public" <?php echo $current_user['is_public'] ? 'checked' : ''; ?>>
                                    Make my profile public
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                        
                        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                            <h4>Account Information</h4>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($current_user['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($current_user['email']); ?></p>
                            <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($current_user['created_at'])); ?></p>
                        </div>
                    </div>

                    <!-- Skills I Offer -->
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
                            Add Skill I Offer
                        </button>
                        
                        <div id="add-skill-offered" class="form-container" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
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

                    <!-- Skills I Want to Learn -->
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
                            Add Skill I Want
                        </button>
                        
                        <div id="add-skill-wanted" class="form-container" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
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
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            if (form) {
                const isVisible = form.style.display !== 'none';
                form.style.display = isVisible ? 'none' : 'block';
                
                if (!isVisible) {
                    const firstInput = form.querySelector('select, input, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }
            }
        }
    </script>
</body>
</html>
