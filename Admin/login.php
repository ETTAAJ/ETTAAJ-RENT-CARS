<?php
require_once 'config.php';

// Already logged in? Redirect
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        $msg = 'Both fields are required.';
    } elseif (ip_blocked($pdo)) {  // Now function exists
        $msg = 'Too many attempts – try again in 15 minutes.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$user]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($pass, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_user']      = $admin['username'];
            $_SESSION['last_regen']      = time();

            // Clear failed attempts
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([get_ip()]);

            header('Location: index.php');
            exit;
        } else {
            log_attempt($pdo);  // Now function exists
            $msg = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body{background:linear-gradient(135deg,#1e3a8a,#1e40af);}</style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="w-full max-w-md p-8 bg-white rounded-xl shadow-2xl space-y-6">
    <h2 class="text-3xl font-bold text-center text-indigo-900">Admin Login</h2>

    <?php if ($msg): ?>
        <div class="p-3 text-sm text-red-700 bg-red-100 border border-red-400 rounded">
            <?=htmlspecialchars($msg)?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit"
                class="w-full py-3 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            Login
        </button>
    </form>
</div>
</body>
</html>