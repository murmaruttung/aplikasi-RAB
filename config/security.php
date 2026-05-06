<?php
// config/security.php - Konfigurasi Keamanan

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerasi session ID setiap 30 menit
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Generate CSRF Token
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        }
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// CSRF Token field untuk form
function csrf_field()
{
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Clean input untuk database
function clean_input($data)
{
    global $conn;
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return mysqli_real_escape_string($conn, $data);
}

// Escape output HTML (XSS prevention)
function h($data)
{
    if (is_null($data)) return '';
    return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Escape untuk JavaScript
function js_escape($data)
{
    if (is_null($data)) return 'null';
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

// Validasi integer
function validate_int($value, $default = 0)
{
    $value = filter_var($value, FILTER_VALIDATE_INT);
    return ($value !== false) ? $value : $default;
}

// Redirect aman
function safe_redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit();
    }
    echo '<script>window.location.href="' . h($url) . '";</script>';
    exit();
}

// Rate limiting sederhana untuk login
function check_rate_limit($key, $max_attempts = 5, $timeout = 300)
{
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];

    if (time() - $attempts['time'] > $timeout) {
        $attempts = ['count' => 0, 'time' => time()];
    }

    if ($attempts['count'] >= $max_attempts) {
        $wait = $timeout - (time() - $attempts['time']);
        return ['allowed' => false, 'wait' => $wait];
    }

    return ['allowed' => true, 'attempts' => $attempts];
}

function increment_rate_limit($key)
{
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    $_SESSION[$key]['count']++;
}
