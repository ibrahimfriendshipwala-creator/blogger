<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database connection settings
$host = "localhost";
$username = "uzgzowzbbecqj";
$password = "pmncdpgxsstl";
$database = "dbd4ct6shmkmsu";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        password varchar(255) NOT NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    
    // Create posts table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id int(11) NOT NULL,
        title varchar(255) NOT NULL,
        content text NOT NULL,
        image varchar(255) DEFAULT NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    
    // Fetch posts with user information
    $query = "SELECT posts.*, users.name as author_name 
              FROM posts 
              LEFT JOIN users ON posts.user_id = users.id 
              ORDER BY posts.created_at DESC";
    $posts = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogger - Home</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to Blogger</h1>
            <nav>
                <button id="theme-toggle" class="btn btn-theme">
                    <span class="light-icon">🌞</span>
                    <span class="dark-icon">🌙</span>
                </button>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="create_post.php" class="btn">Create New Post</a>
                    <a href="logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <?php if(empty($posts)): ?>
                <div class="no-posts">No posts yet. Be the first to create a post!</div>
            <?php else: ?>
                <?php foreach($posts as $post): ?>
                    <article class="post">
                        <div class="post-content-wrapper">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                            <div class="post-meta">
                                <span>By <?php echo htmlspecialchars($post['author_name']); ?></span>
                                <span>Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                            </div>
                            <?php if($post['image']): ?>
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post image">
                            <?php endif; ?>
                            <div class="post-content">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </div>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                <div class="post-actions">
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn">Edit</a>
                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="status-dot"></div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Add this before closing body tag -->
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        // Check for saved theme preference or default to 'light'
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    </script>
</body>
</html>
