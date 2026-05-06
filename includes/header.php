<?php
// includes/header.php - Header Template
if (!isset($page_title)) {
    $page_title = 'SIPER';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - SIPER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <button class="btn btn-primary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-calculator"></i> SIPER
            </a>
            <div class="dropdown ms-auto">
                <button class="btn btn-primary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="uploads/<?= h($_SESSION['foto_profil'] ?? 'default.png') ?>"
                        class="rounded-circle me-2" width="30" height="30"
                        style="object-fit: cover; border: 2px solid rgba(255,255,255,0.5);"
                        alt="Foto Profil" onerror="this.src='uploads/default.png'">
                    <div class="d-none d-md-block text-start">
                        <div class="small fw-semibold"><?= h($_SESSION['nama_lengkap']) ?></div>
                        <div class="small opacity-75"><?= ucfirst(h($_SESSION['role'])) ?></div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li class="dropdown-header">
                        <strong><?= h($_SESSION['nama_lengkap']) ?></strong><br>
                        <small class="text-muted">@<?= h($_SESSION['username']) ?></small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item" href="profile.php#password"><i class="bi bi-key"></i> Ubah Password</a></li>
                    <?php if (is_admin()): ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="master_data.php"><i class="bi bi-gear"></i> Master Data</a></li>
                    <?php endif; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
