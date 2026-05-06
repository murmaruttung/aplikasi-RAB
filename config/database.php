<?php
// config/database.php - Konfigurasi Database

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set ke 1 untuk debugging di development
ini_set('log_errors', 1);

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'perencanaan_keuangan');

// Gunakan mysqli dengan prepared statements
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Koneksi database gagal. Silakan cek konfigurasi.");
}

mysqli_set_charset($conn, 'utf8mb4');
date_default_timezone_set('Asia/Jakarta');
