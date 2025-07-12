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

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header('Location: browse.php');
    exit();
}

$profile_user = $user->getUserById($user_id);
if (!$profile_user || (!$profile_user['is_public'] && $profile_user['id'] != getCurrentUserId())) {
    header('Location: browse.php');
    exit();
}

$user_skills_offered = $skill->getUserSkillsOffered($user_id);
$user_skills_wanted = $skill->getUserSkillsWanted($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['full_name']); ?> - SkillSwap</title>
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
                <div class="user-profile">
                    <div class="profile-header" style="text-align: center; margin-bottom: 40px; padding: 40px; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <h1><?php echo htmlspecialchars($profile_user['full_name']); ?></h1>
                        <p class="username" style="font-size: 18px; color: #666;">@<?php echo htmlspecialchars($profile_user['username']); ?></p>
                        <?php if ($profile_user['location']): ?>
                            <p style="color: #888; margin-top: 10px;">📍 <?php echo htmlspecialchars($profile_user['location']); ?></p>
                        <?php endif; ?>
                        <?php if ($profile_user['availability']): ?>
                            <p style="color: #666; margin-top: 10px;">🕒 Available: <?php echo htmlspecialchars($profile_user['availability']); ?></p>
                        <?php endif; ?>
                        <p style="color: #888; font-size: 14px; margin-top: 15px;">
                            Member since <?php echo date('F Y', strtotime($profile_user['created_at'])); ?>
                        </p>
                    </div>

                    <div class="profile-content" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px;">
                        <!-- Skills Offered -->
                        <div class="dashboard-card">
                            <h3>Skills Offered</h3>
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
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No skills listed yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Skills Wanted -->
                        <div class="dashboard-card">
                            <h3>Skills Wanted</h3>
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
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No skills listed yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($profile_user['id'] != getCurrentUserId()): ?>
                        <div style="text-align: center; margin-top: 40px;">
                            <a href="browse.php" class="btn btn-secondary">← Back to Browse</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
