<?php
require_once 'config.php';

/* -------------------------------------------------
   1. SESSION PROTECTION
   ------------------------------------------------- */
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

/* -------------------------------------------------
   2. CSRF TOKEN
   ------------------------------------------------- */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

/* -------------------------------------------------
   3. ONLY ALLOW POST
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/* -------------------------------------------------
   4. CSRF + ID VALIDATION
   ------------------------------------------------- */
if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
    die("Invalid request.");
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

/* -------------------------------------------------
   5. DELETE IMAGE + DB RECORD
   ------------------------------------------------- */
try {
    // Get image filename
    $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();

    if ($car && !empty($car['image'])) {
        $imagePath = __DIR__ . '/../uploads/' . basename($car['image']);
        if (file_exists($imagePath)) {
            @unlink($imagePath); // Delete image
        }
    }

    // Delete from DB
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php?deleted=1');
    exit;
} catch (Exception $e) {
    error_log("Delete error: " . $e->getMessage());
    header('Location: index.php?error=1');
    exit;
}
?>