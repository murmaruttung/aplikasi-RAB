<?php
// index.php - Dashboard
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

check_login();

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Statistik dengan error handling
$total_pagu = 0;
$total_nominal = 0;
$total_program = 0;
$total_uraian = 0;
$total_blokir = 0;

try {
    $temp_pagu = get_all_pagu();
    if ($temp_pagu) {
        while ($p = mysqli_fetch_assoc($temp_pagu)) {
            $total_pagu++;
            $total_nominal += $p['nominal_pagu'];
        }
    }

    // Hitung program
    $query_program = "SELECT COUNT(*) as total FROM program_pagu pp JOIN pagu_anggaran pa ON pp.pagu_id = pa.id";
    if (!is_admin()) $query_program .= " WHERE pa.created_by = " . get_user_id();
    $res_program = mysqli_query($conn, $query_program);
    if ($res_program) {
        $total_program = mysqli_fetch_assoc($res_program)['total'];
    }

    // Hitung uraian
    $query_uraian = "SELECT COUNT(*) as total, COALESCE(SUM(nilai_blokir), 0) as blokir FROM uraian_anggaran ua JOIN program_pagu pp ON ua.program_id = pp.id JOIN pagu_anggaran pa ON pp.pagu_id = pa.id";
    if (!is_admin()) $query_uraian .= " WHERE pa.created_by = " . get_user_id();
    $res_uraian = mysqli_query($conn, $query_uraian);
    if ($res_uraian) {
        $data_uraian = mysqli_fetch_assoc($res_uraian);
        $total_uraian = $data_uraian['total'];
        $total_blokir = $data_uraian['blokir'];
    }
} catch (Exception $e) {
    // Lanjutkan meskipun ada error
}

$pagu_list = get_all_pagu();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<main class="col-lg-10 ms-auto px-4 py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><i class="bi bi-house-door-fill"></i> Dashboard</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Selamat Datang, <?= h(explode(' ', $_SESSION['nama_lengkap'])[0]) ?>!</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-shield-check"></i> Anda login sebagai <strong><?= ucfirst(h($_SESSION['role'])) ?></strong>
            </p>
        </div>
        <a href="modules/pagu/index.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pagu Baru</a>
    </div>

    <!-- Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small">Total Pagu</h6>
                            <h2 class="text-primary"><?= $total_pagu ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-file-earmark-text fs-4 text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-success h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small">Total Nominal</h6>
                            <h4 class="text-success"><?= format_rupiah($total_nominal) ?></h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-cash-stack fs-4 text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-info h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small">Total Program</h6>
                            <h2 class="text-info"><?= $total_program ?></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-diagram-3 fs-4 text-info"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small">Total Uraian</h6>
                            <h2 class="text-warning"><?= $total_uraian ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 stat-icon"><i class="bi bi-list-ul fs-4 text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pagu -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Pagu Anggaran</h5>
            <span class="badge bg-primary"><?= $total_pagu ?> Pagu</span>
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
                        <?php
                        $no = 1;
                        if ($pagu_list && mysqli_num_rows($pagu_list) > 0):
                            while ($pagu = mysqli_fetch_assoc($pagu_list)):
                        ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= h($pagu['judul_kegiatan']) ?></strong><br><small class="text-muted"><?= format_tanggal($pagu['created_at']) ?></small></td>
                                    <td><?= h($pagu['nama_pagu']) ?></td>
                                    <td><?= h($pagu['nama_unit']) ?></td>
                                    <td><span class="badge bg-info"><?= h($pagu['nama_jenis']) ?></span></td>
                                    <td class="text-end"><?= format_rupiah($pagu['sum_total_program']) ?></td>
                                    <td class="text-end"><strong class="text-success"><?= format_rupiah($pagu['nominal_pagu']) ?></strong></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="modules/pagu/program.php?id=<?= $pagu['id'] ?>" class="btn btn-success" title="Program"><i class="bi bi-list-ul"></i></a>
                                            <a href="modules/pagu/index.php?action=edit&id=<?= $pagu['id'] ?>" class="btn btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <a href="modules/export/all.php?id=<?= $pagu['id'] ?>" class="btn btn-info" target="_blank" title="Export"><i class="bi bi-file-earmark-excel"></i></a>
                                            <a href="modules/pagu/index.php?action=delete&id=<?= $pagu['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
