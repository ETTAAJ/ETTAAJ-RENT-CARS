<?php
require_once 'config.php';

/* -------------------------------------------------
   1. ONLY ALLOW LOGGED-IN USERS (optional safety)
   ------------------------------------------------- */
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

/* -------------------------------------------------
   2. FULL SESSION DESTROY
   ------------------------------------------------- */
$_SESSION = [];

// Delete session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session
session_destroy();

/* -------------------------------------------------
   3. REDIRECT TO MAIN WEBSITE HOMEPAGE
   ------------------------------------------------- */
header('Location: ../index.php');
exit;
?>