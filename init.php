<?php
// Prevent multiple includes
if (defined('INIT_LOADED')) {
    return;
}
define('INIT_LOADED', true);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get language from URL parameter, session, cookie, or default to 'en'
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';

// Validate language code
if (!in_array($lang, ['en', 'fr', 'ar'])) {
    $lang = 'en';
}

// Store language in session
$_SESSION['lang'] = $lang;

// Set cookie only if headers haven't been sent
if (!headers_sent()) {
    setcookie('lang', $lang, time() + (365 * 24 * 60 * 60), '/');
}

// Load language file
$langFile = __DIR__ . '/languages/' . $lang . '.php';

// If language file doesn't exist, fallback to English
if (!file_exists($langFile)) {
    $langFile = __DIR__ . '/languages/en.php';
    $lang = 'en';
}

// Load translations
$text = require $langFile;

// Ensure $text is an array
if (!is_array($text)) {
    $text = [];
}

// Translation helper function
if (!function_exists('t')) {
    function t($key) {
        global $text;
        return $text[$key] ?? $key;
    }
}

// RTL direction helper
if (!function_exists('getDir')) {
    function getDir() {
        global $lang;
        return $lang === 'ar' ? 'rtl' : 'ltr';
    }
}

// Number formatting - always Western format (0-9)
if (!function_exists('formatNumber')) {
    function formatNumber($number, $decimals = 0) {
        // Force Western numerals (0-9) even in Arabic
        return number_format((float)$number, $decimals, '.', ',');
    }
}

// Format phone number - always LTR
if (!function_exists('formatPhone')) {
    function formatPhone($phone) {
        // Remove any non-digit characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        // Format: +212 772 331 080
        if (strpos($cleaned, '+212') === 0) {
            return '+212 ' . substr($cleaned, 4, 3) . ' ' . substr($cleaned, 7, 3) . ' ' . substr($cleaned, 10);
        }
        return $cleaned;
    }
}

?>

