<?php
require_once 'config.php';

// Force correct admin
$username = 'admin';
$password = 'admin123';  // This will be your working password
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO admins (username, password) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE password = ?
    ");
    $stmt->execute([$username, $hash, $hash]);

    echo "<div style='font-family: Arial; padding: 30px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; max-width: 500px; margin: 50px auto; text-align: center;'>
            <h2>Admin Fixed!</h2>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
            <hr>
            <a href='login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Go to Login</a>
          </div>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
