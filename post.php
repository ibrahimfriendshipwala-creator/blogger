<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT posts.*, users.username 
                       FROM posts 
                       JOIN users ON posts.user_id = users.id 
                       WHERE posts.id = ?");
$stmt->execute([$_GET['id']]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - My Blogger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .post-meta {
            color: #666;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .post-content {
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Blogger</h1>
    </header>

    <div class="container">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            <p>By <?= htmlspecialchars($post['username']) ?> on <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
        </div>
        
        <?php if ($post['image_url']): ?>
            <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Post image">
        <?php endif; ?>

        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>

        <a href="index.php" class="btn">Back to Home</a>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn">Edit Post</a>
        <?php endif; ?>
    </div>
</body>
</html> 
