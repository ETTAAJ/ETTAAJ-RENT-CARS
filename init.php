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

// ================================================
// CURRENCY SYSTEM
// ================================================

// Get currency from URL parameter, session, cookie, or default to 'MAD'
$currency_code = $_GET['currency'] ?? $_SESSION['currency'] ?? $_COOKIE['currency'] ?? 'MAD';

// Validate currency code
if (!in_array($currency_code, ['MAD', 'USD', 'EUR'])) {
    $currency_code = 'MAD';
}

// Store currency in session
$_SESSION['currency'] = $currency_code;

// Set cookie only if headers haven't been sent
if (!headers_sent()) {
    setcookie('currency', $currency_code, time() + (365 * 24 * 60 * 60), '/');
}

// Load currency rates from database (if config is loaded)
$currency_rate = 1.0;
$currency_symbol = 'MAD';
$currency_name = 'Moroccan Dirham';

// Try to load currency from database if available
if (defined('CONFIG_LOADED') && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT code, name, symbol, rate_to_mad FROM currencies WHERE code = ? AND is_active = 1");
        $stmt->execute([$currency_code]);
        $currency_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currency_data) {
            $currency_rate = (float)$currency_data['rate_to_mad'];
            $currency_symbol = $currency_data['symbol'];
            $currency_name = $currency_data['name'];
        }
    } catch (PDOException $e) {
        // Fallback to defaults if database error
    }
} else {
    // If database not loaded yet, set defaults based on currency code
    switch ($currency_code) {
        case 'USD':
            $currency_rate = 10.0;
            $currency_symbol = '$';
            $currency_name = 'US Dollar';
            break;
        case 'EUR':
            $currency_rate = 11.0;
            $currency_symbol = '€';
            $currency_name = 'Euro';
            break;
        default:
            $currency_rate = 1.0;
            $currency_symbol = 'MAD';
            $currency_name = 'Moroccan Dirham';
    }
}

// Helper function to get fresh currency data from database
if (!function_exists('getFreshCurrencyData')) {
    function getFreshCurrencyData($code) {
        global $pdo;
        
        if (!defined('CONFIG_LOADED') || !isset($pdo)) {
            // Fallback to defaults if database not available
            switch ($code) {
                case 'USD':
                    return ['rate' => 10.0, 'symbol' => '$', 'name' => 'US Dollar'];
                case 'EUR':
                    return ['rate' => 11.0, 'symbol' => '€', 'name' => 'Euro'];
                default:
                    return ['rate' => 1.0, 'symbol' => 'MAD', 'name' => 'Moroccan Dirham'];
            }
        }
        
        try {
            $stmt = $pdo->prepare("SELECT code, name, symbol, rate_to_mad FROM currencies WHERE code = ? AND is_active = 1");
            $stmt->execute([$code]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                return [
                    'rate' => (float)$data['rate_to_mad'],
                    'symbol' => $data['symbol'],
                    'name' => $data['name']
                ];
            }
        } catch (PDOException $e) {
            // Fallback on error
        }
        
        // Fallback to defaults
        switch ($code) {
            case 'USD':
                return ['rate' => 10.0, 'symbol' => '$', 'name' => 'US Dollar'];
            case 'EUR':
                return ['rate' => 11.0, 'symbol' => '€', 'name' => 'Euro'];
            default:
                return ['rate' => 1.0, 'symbol' => 'MAD', 'name' => 'Moroccan Dirham'];
        }
    }
}

// Currency conversion function - converts MAD to selected currency
if (!function_exists('convertCurrency')) {
    function convertCurrency($amount_mad) {
        global $currency_code;
        
        // Always get fresh currency data from database
        $currency_data = getFreshCurrencyData($currency_code);
        $currency_rate = $currency_data['rate'];
        
        if ($currency_rate <= 0) {
            $currency_rate = 1.0; // Prevent division by zero
        }
        
        return (float)$amount_mad / $currency_rate;
    }
}

// Format price with currency symbol
if (!function_exists('formatPrice')) {
    function formatPrice($amount_mad, $decimals = 0) {
        global $currency_code;
        
        // Always get fresh currency data from database
        $currency_data = getFreshCurrencyData($currency_code);
        $currency_rate = $currency_data['rate'];
        $currency_symbol = $currency_data['symbol'];
        
        if ($currency_rate <= 0) {
            $currency_rate = 1.0; // Prevent division by zero
        }
        
        $converted = (float)$amount_mad / $currency_rate;
        $formatted = formatNumber($converted, $decimals);
        
        // Position symbol based on currency
        if ($currency_code === 'USD' || $currency_code === 'EUR') {
            return $currency_symbol . $formatted;
        }
        return $formatted . ' ' . $currency_symbol;
    }
}

// Get current currency info (always fresh from database)
if (!function_exists('getCurrency')) {
    function getCurrency() {
        global $currency_code;
        
        // Always get fresh currency data from database
        $currency_data = getFreshCurrencyData($currency_code);
        
        return [
            'code' => $currency_code,
            'symbol' => $currency_data['symbol'],
            'name' => $currency_data['name'],
            'rate' => $currency_data['rate']
        ];
    }
}

?>

