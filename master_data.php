<?php
// master_data.php - Master Data Router
require_once 'config/database.php';
require_once 'config/security.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/helpers.php';

check_login();

if (!is_admin()) {
    safe_redirect('index.php');
}

$tab = $_GET['tab'] ?? 'akun';
$current_page = $tab;
$action = $_GET['action'] ?? '';
$id = validate_int($_GET['id'] ?? 0);
$error = '';

// Tentukan handler berdasarkan tab
$handler_file = __DIR__ . '/modules/master/' . $tab . '.php';
if (file_exists($handler_file)) {
    require_once $handler_file;
} else {
    require_once __DIR__ . '/modules/master/akun.php';
}

// Tentukan judul
$titles = ['akun' => 'Akun Belanja', 'jenis' => 'Jenis Pagu', 'unit' => 'Unit Pelaksana', 'user' => 'Manajemen Pengguna'];
$page_title = $titles[$tab] ?? 'Master Data';

// Render halaman
require_once __DIR__ . '/modules/master/templates.php';

include 'includes/header.php';
include 'includes/sidebar.php';
render_master_page($tab, $error, $data_list ?? null, $edit_data ?? null, $page_title);
include 'includes/footer.php';
