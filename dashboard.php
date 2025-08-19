<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Blogger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #333;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .post-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .actions {
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn-primary {
            background: #333;
            color: white;
        }
        .btn-edit {
            background: #4CAF50;
            color: white;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .welcome-bar {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Blogger</h1>
        <nav>
            <a href="index.php" style="color: white; margin-right: 10px;">Home</a>
            <a href="logout.php" style="color: white;">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-bar">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
            <a href="create_post.php" class="btn btn-primary">Create New Post</a>
        </div>

        <h3>Your Posts</h3>
        <?php if (empty($posts)): ?>
            <p>You haven't created any posts yet.</p>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <div class="post-card">
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <p>Created on: <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
                    <p><?= substr(htmlspecialchars($post['content']), 0, 150) ?>...</p>
                    <div class="actions">
                        <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-primary">View</a>
                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-edit">Edit</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html> 
