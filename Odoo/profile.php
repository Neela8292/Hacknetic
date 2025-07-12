<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$current_user = $user->getUserById(getCurrentUserId());
$success = '';
$error = '';

if ($_POST) {
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
            $current_user = $user->getUserById(getCurrentUserId()); // Refresh data
        } else {
            $error = 'Failed to update profile.';
        }
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
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 30px;">My Profile</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST">
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
                        Make my profile public (others can find and contact me)
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
            </form>
            
            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                <h3>Account Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($current_user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($current_user['email']); ?></p>
                <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($current_user['created_at'])); ?></p>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
