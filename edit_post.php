<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection settings
$host = "localhost";
$username = "uzgzowzbbecqj";
$password = "pmncdpgxsstl";
$database = "dbd4ct6shmkmsu";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if post ID is provided
    if(!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the post
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch();

    if(!$post) {
        header("Location: index.php");
        exit();
    }

    // Process form submission
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $image_url = trim($_POST['image_url']);
        
        // Validation
        $errors = [];
        if(empty($title)) {
            $errors[] = "Title is required";
        }
        if(empty($content)) {
            $errors[] = "Content is required";
        }
        
        // Optional image URL validation
        if(!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            $errors[] = "Please enter a valid image URL";
        }

        if(empty($errors)) {
            $sql = "UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            
            if($stmt->execute([$title, $content, $image_url, $post_id, $user_id])) {
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Error updating post";
            }
        }
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Blogger</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="container">
                <h1>Edit Post</h1>
                <nav>
                    <a href="index.php" class="btn">Back to Home</a>
                </nav>
            </div>
        </header>

        <div class="create-post-form">
            <?php if(!empty($errors)): ?>
                <div class="error">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $post_id); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" 
                           value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL (optional):</label>
                    <input type="url" id="image_url" name="image_url" 
                           value="<?php echo htmlspecialchars($post['image']); ?>"
                           onchange="previewImage(this.value)">
                    <?php if(!empty($post['image'])): ?>
                        <img id="preview" class="preview-image" 
                             src="<?php echo htmlspecialchars($post['image']); ?>" 
                             alt="Current post image" style="display: block;">
                    <?php else: ?>
                        <img id="preview" class="preview-image" alt="Preview" style="display: none;">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <button type="submit" class="btn">Update Post</button>
            </form>
        </div>
    </div>

    <script>
        function previewImage(url) {
            const preview = document.getElementById('preview');
            if(url) {
                preview.style.display = 'block';
                preview.src = url;
            } else {
                preview.style.display = 'none';
            }
        }

        // Handle image load errors
        document.getElementById('preview').onerror = function() {
            this.style.display = 'none';
            alert('Error loading image. Please check the URL.');
        };
    </script>
</body>
</html> 
