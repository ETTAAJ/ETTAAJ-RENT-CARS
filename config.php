<?php
// Prevent multiple includes
if (defined('CONFIG_LOADED')) {
    return;
}
define('CONFIG_LOADED', true);

// Load language system first
require_once __DIR__ . '/init.php';

// Initialize database connection
$host = 'localhost';
$db   = 'ettaajrentcars';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Language URL helper function
if (!function_exists('langUrl')) {
    function langUrl($page, $params = []) {
        global $lang;
        
        // Get current URL parameters (like id for car-detail.php, booking.php)
        $currentParams = $_GET;
        
        // Remove lang from current params to avoid duplication
        unset($currentParams['lang']);
        unset($currentParams['ajax']); // Remove ajax parameter
        
        // Merge with provided params (provided params take precedence)
        $finalParams = array_merge($currentParams, $params);
        
        // Set lang parameter - use provided lang or current lang
        if (!isset($finalParams['lang'])) {
            $finalParams['lang'] = $lang;
        }
        
        // Remove hash from params if present
        $hash = '';
        if (isset($finalParams['#'])) {
            $hash = '#' . $finalParams['#'];
            unset($finalParams['#']);
        }
        
        // Build query string
        $query = !empty($finalParams) ? '?' . http_build_query($finalParams) : '';
        return $page . $query . $hash;
    }
}

?>