<?php
require_once 'config.php';

/* -------------------------------------------------
   1. ONLY ALLOW LOGGED-IN ADMIN
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
   3. GET CURRENT ADMIN
   ------------------------------------------------- */
$admin_id = $_SESSION['admin_id'] ?? 0;
$stmt = $pdo->prepare("SELECT username, password FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    session_destroy();
    header('Location: login.php');
    exit;
}

/* -------------------------------------------------
   4. HANDLE FORM
   ------------------------------------------------- */
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $errors[] = "Invalid request.";
    } else {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Validate current password
        if (!password_verify($current, $admin['password'])) {
            $errors[] = "Current password is incorrect.";
        }

        // Validate new password
        if (strlen($new) < 8) {
            $errors[] = "New password must be at least 8 characters.";
        } elseif ($new !== $confirm) {
            $errors[] = "Passwords do not match.";
        }

        // Update if no errors
        if (empty($errors)) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $admin_id]);

            // Log action
            $log = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, ip, created_at) VALUES (?, 'password_change', ?, NOW())");
            $log->execute([$admin_id, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);

            $success = "Password changed successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card { max-width: 500px; margin: 50px auto; }
    .alert { margin-top: 1rem; }
  </style>
</head>
<body class="bg-light">
<div class="container">
  <div class="card shadow">
    <div class="card-header bg-primary text-white text-center">
      <h4>Change Password</h4>
    </div>
    <div class="card-body">
      <p class="text-muted">Logged in as: <strong><?= htmlspecialchars($admin['username']) ?></strong></p>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required minlength="8">
          <small class="text-muted">Minimum 8 characters</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success btn-lg">Change Password</button>
          <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>