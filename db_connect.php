<?php
$host = 'localhost';
$dbname = 'dbd4ct6shmkmsu';
$username = 'uzgzowzbbecqj';  // Use your local database username
$password = 'pmncdpgxsstl';  // Use your local database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
