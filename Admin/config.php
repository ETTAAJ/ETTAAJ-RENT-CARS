<?php
// Admin/config.php – Secure DB + Session + Brute-force protection

/* -------------------------------------------------
   1. SESSION HARDENING
   ------------------------------------------------- */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure',   isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
session_start();

/* -------------------------------------------------
   2. DATABASE CONNECTION (YOUR CODE)
   ------------------------------------------------- */
$host    = '127.0.0.1';
$db      = 'car_rental';
$user    = 'root';
$pass    = '';                 // XAMPP default
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Show detailed error only during setup
    die("Connection failed: " . $e->getMessage());
}

/* -------------------------------------------------
   3. BRUTE-FORCE PROTECTION FUNCTIONS
   ------------------------------------------------- */
function get_ip() {
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function ip_blocked($pdo) {
    $ip = get_ip();
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS attempts 
         FROM login_attempts 
         WHERE ip = ? AND timestamp > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
    );
    $stmt->execute([$ip]);
    $row = $stmt->fetch();
    return ($row && $row['attempts'] >= 5);
}

function log_attempt($pdo) {
    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip) VALUES (?)");
    $stmt->execute([get_ip()]);
}

function clear_old_attempts($pdo) {
    $pdo->prepare("DELETE FROM login_attempts WHERE timestamp < DATE_SUB(NOW(), INTERVAL 15 MINUTE)")
        ->execute();
}

/* -------------------------------------------------
   4. CLEAN OLD ATTEMPTS ON EVERY PAGE LOAD
   ------------------------------------------------- */
clear_old_attempts($pdo);
?>