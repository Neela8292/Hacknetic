<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Skill.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$skill = new Skill($db);

// Handle search
$search_results = [];
$search_skill = $_GET['skill'] ?? '';
$search_location = $_GET['location'] ?? '';

if ($search_skill || $search_location) {
    $search_results = $user->searchUsers($search_skill, $search_location);
}

$all_skills = $skill->getAllSkills();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Swap Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>SkillSwap</h2>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="browse.php" class="nav-link">Browse</a>
                    <a href="profile.php" class="nav-link">Profile</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <section class="hero">
            <div class="hero-content">
                <h1>Exchange Skills, Build Connections</h1>
                <p>Connect with others to share knowledge and learn new skills through our skill swap platform.</p>
                <?php if (!isLoggedIn()): ?>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-primary">Get Started</a>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="search-section">
            <div class="container">
                <h2>Find Skills & People</h2>
                <form class="search-form" method="GET">
                    <div class="search-inputs">
                        <input type="text" name="skill" placeholder="Search by skill (e.g., JavaScript, Guitar)" 
                               value="<?php echo htmlspecialchars($search_skill); ?>">
                        <input type="text" name="location" placeholder="Location" 
                               value="<?php echo htmlspecialchars($search_location); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <?php if (!empty($search_results)): ?>
                    <div class="search-results">
                        <h3>Search Results (<?php echo count($search_results); ?> found)</h3>
                        <div class="user-grid">
                            <?php foreach ($search_results as $result_user): ?>
                                <div class="user-card">
                                    <div class="user-info">
                                        <h4><?php echo htmlspecialchars($result_user['full_name']); ?></h4>
                                        <p class="username">@<?php echo htmlspecialchars($result_user['username']); ?></p>
                                        <?php if ($result_user['location']): ?>
                                            <p class="location"><?php echo htmlspecialchars($result_user['location']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (isLoggedIn()): ?>
                                        <a href="user-profile.php?id=<?php echo $result_user['id']; ?>" class="btn btn-small">View Profile</a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($search_skill || $search_location): ?>
                    <div class="no-results">
                        <p>No users found matching your search criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2>How It Works</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">👤</div>
                        <h3>Create Profile</h3>
                        <p>Set up your profile with skills you offer and skills you want to learn.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔍</div>
                        <h3>Find Matches</h3>
                        <p>Browse and search for people with complementary skills.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🤝</div>
                        <h3>Make Swaps</h3>
                        <p>Send swap requests and start learning from each other.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⭐</div>
                        <h3>Rate & Review</h3>
                        <p>Leave feedback after successful skill exchanges.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="popular-skills">
            <div class="container">
                <h2>Popular Skills</h2>
                <div class="skills-tags">
                    <?php foreach (array_slice($all_skills, 0, 15) as $skill_item): ?>
                        <span class="skill-tag">
                            <a href="?skill=<?php echo urlencode($skill_item['name']); ?>">
                                <?php echo htmlspecialchars($skill_item['name']); ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SkillSwap Platform. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
