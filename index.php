<?php
// index.php - Dashboard
require_once 'config/database.php';
require_once 'config/security.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/helpers.php';

check_login();

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Statistik
$total_pagu = $total_nominal = $total_program = $total_uraian = $total_blokir = 0;

$pagu_stmt = get_all_pagu();
$all_pagu = [];
if ($pagu_stmt) {
    while ($p = fetch($pagu_stmt)) {
        $total_pagu++;
        $total_nominal += $p['nominal_pagu'];
        $all_pagu[] = $p;
    }
}

// Hitung program
$query_program = "SELECT COUNT(*) as total FROM program_pagu pp JOIN pagu_anggaran pa ON pp.pagu_id = pa.id";
if (!is_admin()) {
    $stmt_program = query($query_program . " WHERE pa.created_by = ?", [get_user_id()]);
} else {
    $stmt_program = query($query_program);
}
$total_program = $stmt_program ? fetch($stmt_program)['total'] : 0;

// Hitung uraian dan blokir
$query_uraian = "SELECT COUNT(*) as total, COALESCE(SUM(nilai_blokir), 0) as blokir FROM uraian_anggaran ua JOIN program_pagu pp ON ua.program_id = pp.id JOIN pagu_anggaran pa ON pp.pagu_id = pa.id";
if (!is_admin()) {
    $stmt_uraian = query($query_uraian . " WHERE pa.created_by = ?", [get_user_id()]);
} else {
    $stmt_uraian = query($query_uraian);
}
$data_uraian = $stmt_uraian ? fetch($stmt_uraian) : ['total' => 0, 'blokir' => 0];
$total_uraian = $data_uraian['total'];
$total_blokir = $data_uraian['blokir'];

$pagu_list = get_all_pagu();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-lg-10 ms-auto px-4 py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><i class="bi bi-house-door-fill"></i> Dashboard</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Selamat Datang, <?= h(explode(' ', $_SESSION['nama_lengkap'])[0]) ?>! 👋</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-shield-check"></i> Anda login sebagai <strong><?= ucfirst(h($_SESSION['role'])) ?></strong>
                <?php if (!is_admin()): ?><span class="badge bg-secondary ms-2">Akses Terbatas</span><?php endif; ?>
            </p>
        </div>
        <a href="modules/pagu/index.php" class="btn btn-primary btn-lg"><i class="bi bi-plus-lg"></i> Buat Pagu Baru</a>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted text-uppercase small mb-1">Total Pagu</h6>
                            <h2 class="text-primary mb-0"><?= $total_pagu ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-file-earmark-text fs-4 text-primary"></i></div>
                    </div>
                    <small class="text-muted">Pagu Anggaran</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-success h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted text-uppercase small mb-1">Total Nominal</h6>
                            <h4 class="text-success mb-0"><?= format_rupiah($total_nominal) ?></h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-cash-stack fs-4 text-success"></i></div>
                    </div>
                    <small class="text-muted">Akumulasi Semua Pagu</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-info h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted text-uppercase small mb-1">Total Program</h6>
                            <h2 class="text-info mb-0"><?= $total_program ?></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-diagram-3 fs-4 text-info"></i></div>
                    </div>
                    <small class="text-muted">Jumlah Program</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted text-uppercase small mb-1">Total Uraian</h6>
                            <h2 class="text-warning mb-0"><?= $total_uraian ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-list-ul fs-4 text-warning"></i></div>
                    </div>
                    <small class="text-muted">Blokir: <strong class="text-danger"><?= format_rupiah($total_blokir) ?></strong></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pagu -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Pagu Anggaran</h5>
            <span class="badge bg-primary fs-6"><?= $total_pagu ?> Pagu</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Kegiatan</th>
                            <th>Nama Pagu</th>
                            <th>Unit</th>
                            <th>Jenis</th>
                            <th>Total Program</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $pagu_data = $pagu_list ? fetchAll($pagu_list) : [];
                        if (count($pagu_data) > 0): foreach ($pagu_data as $pagu): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= h($pagu['judul_kegiatan']) ?></strong><br><small class="text-muted"><i class="bi bi-calendar3"></i> <?= format_tanggal($pagu['created_at']) ?></small></td>
                                    <td><?= h($pagu['nama_pagu']) ?></td>
                                    <td><span class="text-nowrap"><i class="bi bi-building text-muted"></i> <?= h($pagu['nama_unit']) ?></span></td>
                                    <td><span class="badge bg-info"><?= h($pagu['nama_jenis']) ?></span></td>
                                    <td class="text-end"><?= format_rupiah($pagu['total_program']) ?></td>
                                    <td class="text-end"><strong class="text-success"><?= format_rupiah($pagu['nominal_pagu']) ?></strong></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="modules/pagu/program.php?id=<?= $pagu['id'] ?>" class="btn btn-success" title="Program & Uraian"><i class="bi bi-list-ul"></i></a>
                                            <a href="modules/pagu/index.php?action=edit&id=<?= $pagu['id'] ?>" class="btn btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <a href="modules/export/all.php?id=<?= $pagu['id'] ?>" class="btn btn-info" target="_blank" title="Export Excel"><i class="bi bi-file-earmark-excel"></i></a>
                                            <a href="modules/pagu/index.php?action=delete&id=<?= $pagu['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus pagu ini? Semua program dan uraian akan ikut terhapus.')" title="Hapus"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        <h5>Belum ada data pagu anggaran</h5>
                                        <p>Mulai dengan membuat pagu anggaran baru</p><a href="modules/pagu/index.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pagu Pertama</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (count($pagu_data) > 0): ?>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">Total Keseluruhan</td>
                                <td class="text-end"><?= $total_program ?></td>
                                <td class="text-end text-success"><?= format_rupiah($total_nominal) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <?php if (is_admin()): ?>
        <div class="row g-3 mt-3">
            <div class="col-md-4"><a href="master_data.php?tab=akun" class="text-decoration-none">
                    <div class="card bg-light border-primary quick-access-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3"><i class="bi bi-journal-text fs-4 text-primary"></i></div>
                            <div>
                                <h6 class="mb-1">Akun Belanja</h6><?php $ca_stmt = query("SELECT COUNT(*) as total FROM akun_belanja"); $ca = $ca_stmt ? fetch($ca_stmt) : ['total' => 0]; ?><small class="text-muted"><?= $ca['total'] ?> akun tersedia</small>
                            </div><i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </div>
                </a></div>
            <div class="col-md-4"><a href="master_data.php?tab=unit" class="text-decoration-none">
                    <div class="card bg-light border-success quick-access-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3"><i class="bi bi-building fs-4 text-success"></i></div>
                            <div>
                                <h6 class="mb-1">Unit Pelaksana</h6><?php $cu_stmt = query("SELECT COUNT(*) as total FROM unit_pelaksana"); $cu = $cu_stmt ? fetch($cu_stmt) : ['total' => 0]; ?><small class="text-muted"><?= $cu['total'] ?> unit tersedia</small>
                            </div><i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </div>
                </a></div>
            <div class="col-md-4"><a href="master_data.php?tab=user" class="text-decoration-none">
                    <div class="card bg-light border-info quick-access-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3"><i class="bi bi-people fs-4 text-info"></i></div>
                            <div>
                                <h6 class="mb-1">Pengguna</h6><?php $cuser_stmt = query("SELECT COUNT(*) as total FROM pengguna WHERE is_active=1"); $cuser = $cuser_stmt ? fetch($cuser_stmt) : ['total' => 0]; ?><small class="text-muted"><?= $cuser['total'] ?> user aktif</small>
                            </div><i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </div>
                </a></div>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
