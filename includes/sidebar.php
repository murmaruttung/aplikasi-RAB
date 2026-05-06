<?php
// includes/sidebar.php - Sidebar Template
$current_page = $current_page ?? '';
?>
<div class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="bi bi-list"></i> Menu Navigasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'dashboard' ? 'active' : '' ?>" href="index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'pagu' ? 'active' : '' ?>" href="modules/pagu/index.php">
                    <i class="bi bi-file-earmark-text"></i> Pagu Anggaran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'profile' ? 'active' : '' ?>" href="profile.php">
                    <i class="bi bi-person"></i> Profil Saya
                </a>
            </li>
        </ul>

        <?php if (is_admin()): ?>
            <hr>
            <small class="text-muted ms-3 fw-bold text-uppercase small">Master Data</small>
            <ul class="nav flex-column mt-2">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'akun' ? 'active' : '' ?>" href="master_data.php?tab=akun">
                        <i class="bi bi-journal-text"></i> Akun Belanja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'jenis' ? 'active' : '' ?>" href="master_data.php?tab=jenis">
                        <i class="bi bi-tags"></i> Jenis Pagu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'unit' ? 'active' : '' ?>" href="master_data.php?tab=unit">
                        <i class="bi bi-building"></i> Unit Pelaksana
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'user' ? 'active' : '' ?>" href="master_data.php?tab=user">
                        <i class="bi bi-people"></i> Pengguna
                    </a>
                </li>
            </ul>
        <?php endif; ?>

        <hr>
        <div class="px-3 py-2">
            <small class="text-muted">
                <i class="bi bi-person-badge"></i>
                Login: <strong><?= ucfirst(h($_SESSION['role'])) ?></strong>
            </small><br>
            <small class="text-muted">
                <i class="bi bi-clock"></i>
                <?= date('d/m/Y H:i') ?>
            </small>
        </div>
    </div>
</div>
