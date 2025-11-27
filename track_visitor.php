<?php
require_once 'config.php';

// ================================================
// SECURITY: Prevent direct access and abuse
// ================================================

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validate Origin/Referer to prevent CSRF
$allowed_origins = [
    'http://localhost',
    'https://www.ettaajrentcars.ma',
    'https://ettaajrentcars.ma',
    'http://www.ettaajrentcars.ma',
    'http://ettaajrentcars.ma'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Check if origin is allowed (for CORS requests)
if ($origin && !in_array($origin, $allowed_origins)) {
    // Check referer as fallback
    $referer_domain = parse_url($referer, PHP_URL_HOST);
    $allowed_domains = ['localhost', 'ettaajrentcars.ma', 'www.ettaajrentcars.ma'];
    
    if (!$referer_domain || !in_array($referer_domain, $allowed_domains)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
        exit;
    }
}

// Rate limiting: Check request frequency per IP
function checkRateLimit($pdo, $ip_address) {
    // Maximum 10 requests per minute per IP
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM visitor_data 
        WHERE ip_address = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    ");
    $stmt->execute([$ip_address]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && (int)$result['count'] >= 10) {
        return false; // Rate limit exceeded
    }
    return true;
}

// Get IP address (handle proxies)
function getRealIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Handle comma-separated IPs (from proxies)
            if (strpos($ip, ',') !== false) {
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
            }
            // Validate IP
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

$ip_address = getRealIP();

// Check rate limit
if (!checkRateLimit($pdo, $ip_address)) {
    http_response_code(429);
    header('Content-Type: application/json');
    header('Retry-After: 60');
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please try again later.']);
    exit;
}

// Validate input size (prevent large payload attacks)
$max_input_size = 10000; // 10KB max
$content_length = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($content_length > $max_input_size) {
    http_response_code(413);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Payload too large']);
    exit;
}

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Set appropriate headers for both JSON and sendBeacon requests
if (isset($_SERVER['HTTP_CONTENT_TYPE']) && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/json') !== false) {
    header('Content-Type: application/json');
} else {
    header('Content-Type: text/plain');
}

// Prevent multiple includes
if (defined('TRACK_LOADED')) {
    return;
}
define('TRACK_LOADED', true);

// Get data from POST request (handles both JSON and sendBeacon)
$rawInput = file_get_contents('php://input');

// Validate input is not empty
if (empty($rawInput) || strlen($rawInput) > $max_input_size) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$data = json_decode($rawInput, true);

// If JSON decode failed, the data might be sent as plain text by sendBeacon
if (!$data && !empty($rawInput)) {
    // Try to decode again (sometimes sendBeacon sends as string)
    $data = json_decode($rawInput, true);
    // If still fails, try to parse as plain JSON string
    if (!$data) {
        $data = json_decode(stripslashes($rawInput), true);
    }
}

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data format']);
    exit;
}

try {
    // Get visitor information
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $referrer = $_SERVER['HTTP_REFERER'] ?? null;
    
    // Sanitize and validate page_url
    $page_url = $data['page_url'] ?? $referrer ?? null;
    if ($page_url) {
        $page_url = filter_var($page_url, FILTER_SANITIZE_URL);
        if (!filter_var($page_url, FILTER_VALIDATE_URL)) {
            $page_url = null;
        }
        // Limit length
        if (strlen($page_url) > 500) {
            $page_url = substr($page_url, 0, 500);
        }
    }
    
    // Sanitize referrer
    if ($referrer) {
        $referrer = filter_var($referrer, FILTER_SANITIZE_URL);
        if (!filter_var($referrer, FILTER_VALIDATE_URL)) {
            $referrer = null;
        }
        if (strlen($referrer) > 500) {
            $referrer = substr($referrer, 0, 500);
        }
    }
    
    // Validate and sanitize session ID
    $session_id = $data['session_id'] ?? $_COOKIE['visitor_session_id'] ?? null;
    if ($session_id) {
        // Session ID should be 32 hex characters (16 bytes = 32 hex chars)
        if (!preg_match('/^[a-f0-9]{32}$/i', $session_id)) {
            $session_id = null; // Invalid format, generate new one
        }
    }
    
    if (!$session_id) {
        $session_id = bin2hex(random_bytes(16));
        // Set cookie for session tracking (expires in 24 hours, httpOnly, secure in production)
        if (!headers_sent()) {
            $cookie_options = [
                'expires' => time() + (24 * 60 * 60),
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $cookie_options['secure'] = true;
            }
            setcookie('visitor_session_id', $session_id, $cookie_options);
        }
    }
    
    // Extract and sanitize data with length limits
    $name = isset($data['name']) && trim($data['name']) !== '' ? trim($data['name']) : null;
    if ($name) {
        // Sanitize name: remove HTML tags and special characters
        $name = htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8');
        if (strlen($name) > 100) {
            $name = substr($name, 0, 100);
        }
        if (empty(trim($name))) {
            $name = null;
        }
    }
    
    $email = isset($data['email']) && trim($data['email']) !== '' ? trim($data['email']) : null;
    if ($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $email = null;
        }
    }
    
    $phone = isset($data['phone']) && trim($data['phone']) !== '' ? trim($data['phone']) : null;
    if ($phone) {
        // Remove non-numeric characters except +, -, spaces, parentheses
        $phone = preg_replace('/[^0-9+\-() ]/', '', $phone);
        if (strlen($phone) > 20) {
            $phone = substr($phone, 0, 20);
        }
        if (empty($phone)) {
            $phone = null;
        }
    }
    
    // Sanitize cookies data
    $cookies_data = null;
    if (isset($data['cookies']) && is_array($data['cookies'])) {
        // Limit cookies array size
        if (count($data['cookies']) <= 50) {
            $cookies_json = json_encode($data['cookies']);
            if ($cookies_json && strlen($cookies_json) <= 2000) {
                $cookies_data = $cookies_json;
            }
        }
    }
    
    // Sanitize user agent
    if ($user_agent && strlen($user_agent) > 500) {
        $user_agent = substr($user_agent, 0, 500);
    }
    
    // Block suspicious user agents (bots, scrapers, etc.)
    $suspicious_patterns = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
        'python', 'java', 'perl', 'ruby', 'go-http', 'scrapy',
        'headless', 'phantom', 'selenium', 'webdriver'
    ];
    
    if ($user_agent) {
        $ua_lower = strtolower($user_agent);
        foreach ($suspicious_patterns as $pattern) {
            if (strpos($ua_lower, $pattern) !== false) {
                // Allow legitimate browsers that might contain these words
                $legitimate_browsers = ['chrome', 'firefox', 'safari', 'edge', 'opera', 'msie'];
                $is_legitimate = false;
                foreach ($legitimate_browsers as $browser) {
                    if (strpos($ua_lower, $browser) !== false) {
                        $is_legitimate = true;
                        break;
                    }
                }
                if (!$is_legitimate) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Forbidden']);
                    exit;
                }
            }
        }
    }
    
    // Check if we should track this request
    // Only track if there's meaningful data (name, email, phone) OR if it's a form submit
    $hasMeaningfulData = ($name || $email || $phone);
    $isFormSubmit = isset($data['form_submit']) && $data['form_submit'] === true;
    
    // If no meaningful data and not a form submit, only update existing record or skip
    if (!$hasMeaningfulData && !$isFormSubmit) {
        // Check if session exists in last 24 hours
        $checkStmt = $pdo->prepare("
            SELECT id FROM visitor_data 
            WHERE session_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY created_at DESC LIMIT 1
        ");
        $checkStmt->execute([$session_id]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing record with latest page URL
            $updateStmt = $pdo->prepare("
                UPDATE visitor_data 
                SET page_url = ?, referrer = ?, cookies_data = COALESCE(?, cookies_data)
                WHERE id = ?
            ");
            $updateStmt->execute([$page_url, $referrer, $cookies_data, $existing['id']]);
            echo json_encode(['success' => true, 'message' => 'Updated', 'action' => 'update']);
            exit;
        } else {
            // No existing session and no meaningful data - skip tracking
            echo json_encode(['success' => true, 'message' => 'Skipped - no data', 'action' => 'skip']);
            exit;
        }
    }
    
    // Find existing record by: 1) email/phone, 2) session_id (last 24h), 3) IP + user agent (last 1 hour)
    $existing = null;
    
    // Priority 1: Check by email or phone
    if ($email || $phone) {
        $checkStmt = $pdo->prepare("SELECT id FROM visitor_data WHERE (email = ? OR phone = ?) ORDER BY created_at DESC LIMIT 1");
        $checkStmt->execute([$email ?: '', $phone ?: '']);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Priority 2: Check by session ID (last 24 hours)
    if (!$existing && $session_id) {
        $checkStmt = $pdo->prepare("
            SELECT id FROM visitor_data 
            WHERE session_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY created_at DESC LIMIT 1
        ");
        $checkStmt->execute([$session_id]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Priority 3: Check by IP + User Agent (last 1 hour) - only if no email/phone/session
    if (!$existing && $ip_address && $user_agent && !$email && !$phone) {
        $checkStmt = $pdo->prepare("
            SELECT id FROM visitor_data 
            WHERE ip_address = ? AND user_agent = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY created_at DESC LIMIT 1
        ");
        $checkStmt->execute([$ip_address, $user_agent]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // If exists, update the record; otherwise insert new
    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE visitor_data 
            SET 
                name = COALESCE(?, name),
                email = COALESCE(?, email),
                phone = COALESCE(?, phone),
                cookies_data = COALESCE(?, cookies_data),
                page_url = ?,
                referrer = ?,
                ip_address = COALESCE(?, ip_address),
                user_agent = COALESCE(?, user_agent),
                session_id = COALESCE(?, session_id)
            WHERE id = ?
        ");
        $stmt->execute([
            $name, $email, $phone, $cookies_data, 
            $page_url, $referrer, $ip_address, $user_agent, 
            $session_id, $existing['id']
        ]);
        echo json_encode(['success' => true, 'message' => 'Updated', 'action' => 'update']);
    } else {
        // Only insert if there's meaningful data
        if ($hasMeaningfulData || $isFormSubmit) {
            $stmt = $pdo->prepare("
                INSERT INTO visitor_data 
                (ip_address, user_agent, name, email, phone, cookies_data, page_url, referrer, session_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $ip_address, $user_agent, $name, $email, $phone, 
                $cookies_data, $page_url, $referrer, $session_id
            ]);
            echo json_encode(['success' => true, 'message' => 'Inserted', 'action' => 'insert']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Skipped - no data', 'action' => 'skip']);
        }
    }
    
} catch (PDOException $e) {
    // Log error but don't expose database details
    error_log("Visitor tracking DB error [IP: {$ip_address}]: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
} catch (Exception $e) {
    // Log error but don't expose details
    error_log("Visitor tracking error [IP: {$ip_address}]: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

