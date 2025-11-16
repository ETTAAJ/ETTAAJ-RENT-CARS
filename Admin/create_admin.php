<?php
require_once 'config.php';

$username = 'admin';
$password = 'admin123';  // Change this later!
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hash]);
    echo "<h3>Admin Created Successfully!</h3>";
    echo "Username: <strong>$username</strong><br>";
    echo "Password: <strong>$password</strong><br><br>";
    echo "<a href='login.php' class='btn btn-primary'>Go to Login</a>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>