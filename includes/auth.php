<?php
// includes/auth.php - Autentikasi & Otorisasi

// Jangan require database.php lagi karena sudah di-require di file utama
// require_once __DIR__ . '/../config/database.php';
// require_once __DIR__ . '/../config/security.php';

// Check login
function check_login()
{
    if (!isset($_SESSION['user_id'])) {
        safe_redirect('login.php');
    }
}

// Check admin
function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Get current user ID
function get_user_id()
{
    return $_SESSION['user_id'] ?? 0;
}

// Get current user data
function get_current_user_data()
{
    global $conn;
    $user_id = get_user_id();
    if (!$user_id) return null;

    $query = "SELECT * FROM pengguna WHERE id = " . intval($user_id);
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
