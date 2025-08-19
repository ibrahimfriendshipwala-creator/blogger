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

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $image_url = trim($_POST['image_url']);
        $user_id = $_SESSION['user_id'];
        
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
            $sql = "INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if($stmt->execute([$user_id, $title, $content, $image_url])) {
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Error creating post";
            }
        }
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post - Blogger</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .create-post-form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            height: 300px;
            resize: vertical;
        }

        .preview-image {
            max-width: 300px;
            margin-top: 10px;
            display: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Create New Post</h1>
            <nav>
                <a href="index.php" class="btn">Back to Home</a>
            </nav>
        </header>

        <div class="create-post-form">
            <?php if(!empty($errors)): ?>
                <div class="error">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL (optional):</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>" 
                           onchange="previewImage(this.value)">
                    <img id="preview" class="preview-image" alt="Preview">
                </div>

                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn">Create Post</button>
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
