<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if post ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

try {
    // First, get the post to check ownership and get image URL
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if ($post) {
        // Delete the associated image if it exists
        if ($post['image_url'] && file_exists($post['image_url'])) {
            unlink($post['image_url']);
        }

        // Delete the post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    }

    header("Location: dashboard.php");
    exit();
} catch(PDOException $e) {
    die("Error deleting post: " . $e->getMessage());
}
?> 
