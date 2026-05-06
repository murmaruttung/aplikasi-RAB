<?php
// modules/pagu/program.php - Program & Uraian
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/helpers.php';

check_login();

$page_title = 'Program & Uraian';
$current_page = 'pagu';

$pagu_id = validate_int($_GET['id'] ?? 0);
if (!$pagu_id) safe_redirect('../../index.php');

$pagu = get_pagu_by_id($pagu_id);
if (!$pagu) safe_redirect('../../index.php');

// Tambah Program
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_program'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $nama_program = clean_input($_POST['nama_program']);
        mysqli_query($conn, "INSERT INTO program_pagu (pagu_id, nama_program, created_by) VALUES ($pagu_id, '$nama_program', " . get_user_id() . ")");
        update_nominal_pagu($pagu_id);
        safe_redirect("program.php?id=$pagu_id");
    }
}

// Edit Program
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_program'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $program_id = validate_int($_POST['program_id']);
        $nama_program = clean_input($_POST['nama_program']);
        mysqli_query($conn, "UPDATE program_pagu SET nama_program='$nama_program' WHERE id=$program_id");
        safe_redirect("program.php?id=$pagu_id");
    }
}

// Hapus Program
if (isset($_GET['hapus_program'])) {
    $program_id = validate_int($_GET['hapus_program']);
    mysqli_query($conn, "DELETE FROM program_pagu WHERE id=$program_id");
    update_nominal_pagu($pagu_id);
    safe_redirect("program.php?id=$pagu_id");
}

// Tambah Uraian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_uraian'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $program_id = validate_int($_POST['program_id']);
        $akun_id = validate_int($_POST['akun_id']);
        $uraian_kegiatan = clean_input($_POST['uraian_kegiatan']);
        $volume = floatval($_POST['volume']);
        $satuan = clean_input($_POST['satuan']);
        $harga_satuan = parse_angka($_POST['harga_satuan']);

        mysqli_query($conn, "INSERT INTO uraian_anggaran (program_id, akun_id, uraian_kegiatan, volume, satuan, harga_satuan, created_by) VALUES ($program_id, $akun_id, '$uraian_kegiatan', $volume, '$satuan', $harga_satuan, " . get_user_id() . ")");
        update_total_program($program_id);
        update_nominal_pagu($pagu_id);
        safe_redirect("program.php?id=$pagu_id");
    }
}

// Edit Uraian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_uraian'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $uraian_id = validate_int($_POST['uraian_id']);
        $program_id = validate_int($_POST['program_id']);
        $akun_id = validate_int($_POST['akun_id']);
        $uraian_kegiatan = clean_input($_POST['uraian_kegiatan']);
        $volume = floatval($_POST['volume']);
        $satuan = clean_input($_POST['satuan']);
        $harga_satuan = parse_angka($_POST['harga_satuan']);

        mysqli_query($conn, "UPDATE uraian_anggaran SET akun_id=$akun_id, uraian_kegiatan='$uraian_kegiatan', volume=$volume, satuan='$satuan', harga_satuan=$harga_satuan WHERE id=$uraian_id");
        update_total_program($program_id);
        update_nominal_pagu($pagu_id);
        safe_redirect("program.php?id=$pagu_id");
    }
}

// Hapus Uraian
if (isset($_GET['hapus_uraian'])) {
    $uraian_id = validate_int($_GET['hapus_uraian']);
    $res = mysqli_query($conn, "SELECT program_id FROM uraian_anggaran WHERE id=$uraian_id");
    $uraian = mysqli_fetch_assoc($res);
    mysqli_query($conn, "DELETE FROM uraian_anggaran WHERE id=$uraian_id");
    if ($uraian) update_total_program($uraian['program_id']);
    update_nominal_pagu($pagu_id);
    safe_redirect("program.php?id=$pagu_id");
}

// Blokir Uraian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blokir_uraian'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $uraian_id = validate_int($_POST['uraian_id']);
        $nilai_blokir = parse_angka($_POST['nilai_blokir']);
        $res = mysqli_query($conn, "SELECT jumlah_harga FROM uraian_anggaran WHERE id=$uraian_id");
        $item = mysqli_fetch_assoc($res);
        $status = ($nilai_blokir >= $item['jumlah_harga']) ? 'penuh' : 'sebagian';
        mysqli_query($conn, "UPDATE uraian_anggaran SET is_blokir=1, nilai_blokir=$nilai_blokir, status_blokir='$status' WHERE id=$uraian_id");
        safe_redirect("program.php?id=$pagu_id");
    }
}

// Buka Blokir
if (isset($_GET['buka_blokir'])) {
    $uraian_id = validate_int($_GET['buka_blokir']);
    mysqli_query($conn, "UPDATE uraian_anggaran SET is_blokir=0, nilai_blokir=0, status_blokir='' WHERE id=$uraian_id");
    safe_redirect("program.php?id=$pagu_id");
}

// Import Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_excel'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $program_id = validate_int($_POST['program_id_import']);
        $data = $_POST['import_data'];
        $lines = explode("\n", trim($data));
        $current_akun_kode = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = preg_split('/\t+/', $line);

            if (count($parts) >= 2 && preg_match('/^\d/', $parts[0])) {
                $current_akun_kode = trim($parts[0]);
                $akun_res = mysqli_query($conn, "SELECT id FROM akun_belanja WHERE kode_akun='$current_akun_kode'");
                $akun = mysqli_fetch_assoc($akun_res);
                if ($akun && count($parts) >= 6) {
                    $uraian = clean_input(trim($parts[2]));
                    $vol = floatval(str_replace(',', '.', $parts[3]));
                    $sat = clean_input(trim($parts[4]));
                    $hrg = parse_angka($parts[5]);
                    mysqli_query($conn, "INSERT INTO uraian_anggaran (program_id, akun_id, uraian_kegiatan, volume, satuan, harga_satuan, created_by) VALUES ($program_id, {$akun['id']}, '$uraian', $vol, '$sat', $hrg, " . get_user_id() . ")");
                }
            } elseif (count($parts) >= 4 && !empty($current_akun_kode)) {
                $akun_res = mysqli_query($conn, "SELECT id FROM akun_belanja WHERE kode_akun='$current_akun_kode'");
                $akun = mysqli_fetch_assoc($akun_res);
                if ($akun) {
                    $uraian = clean_input(trim($parts[0]));
                    $vol = floatval(str_replace(',', '.', $parts[1]));
                    $sat = clean_input(trim($parts[2]));
                    $hrg = parse_angka($parts[3]);
                    mysqli_query($conn, "INSERT INTO uraian_anggaran (program_id, akun_id, uraian_kegiatan, volume, satuan, harga_satuan, created_by) VALUES ($program_id, {$akun['id']}, '$uraian', $vol, '$sat', $hrg, " . get_user_id() . ")");
                }
            }
        }

        update_total_program($program_id);
        update_nominal_pagu($pagu_id);
        safe_redirect("program.php?id=$pagu_id&msg=imported");
    }
}

$programs = get_programs_by_pagu($pagu_id);
$akun_list = get_all_akun();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<main class="col-lg-10 ms-auto px-4 py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../../index.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Pagu Anggaran</a></li>
            <li class="breadcrumb-item active">Program & Uraian</li>
        </ol>
    </nav>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'imported'): ?>
        <?= alert('success', 'Data berhasil diimport dari Excel!') ?>
    <?php endif; ?>

    <!-- Info Pagu -->
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1"><?= h($pagu['judul_kegiatan']) ?></h4>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="badge bg-primary"><i class="bi bi-building"></i> <?= h($pagu['nama_unit']) ?></span>
                        <span class="badge bg-info"><i class="bi bi-tag"></i> <?= h($pagu['nama_jenis']) ?></span>
                        <span class="badge bg-success"><i class="bi bi-cash"></i> <?= format_rupiah($pagu['nominal_pagu']) ?></span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../export/all.php?id=<?= $pagu_id ?>" class="btn btn-info" target="_blank"><i class="bi bi-file-earmark-excel"></i> Export Semua</a>
                    <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah Program -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Program Baru</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-9"><input type="text" name="nama_program" class="form-control" placeholder="Masukkan nama program..." required></div>
                <div class="col-md-3"><button type="submit" name="add_program" class="btn btn-success w-100"><i class="bi bi-plus-lg"></i> Tambah Program</button></div>
            </form>
        </div>
    </div>

    <!-- Daftar Program -->
    <?php if (mysqli_num_rows($programs) > 0): ?>
        <?php $no = 1;
        while ($program = mysqli_fetch_assoc($programs)): ?>
            <div class="card mb-3 program-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#program<?= $program['id'] ?>" aria-expanded="true" style="cursor:pointer;">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="program-number"><?= $no++ ?>.</span>
                        <span class="program-name ms-2"><?= h($program['nama_program']) ?></span>
                        <span class="badge bg-primary ms-3"><?= format_rupiah($program['total_uraian']) ?></span>
                    </div>
                    <div class="program-actions" onclick="event.stopPropagation();">
                        <button class="btn btn-sm btn-outline-warning" onclick="editProgram(<?= $program['id'] ?>, <?= js_escape($program['nama_program']) ?>)" title="Edit"><i class="bi bi-pencil"></i> Edit</button>
                        <a href="?id=<?= $pagu_id ?>&hapus_program=<?= $program['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete('Yakin hapus program ini?')" title="Hapus"><i class="bi bi-trash"></i> Hapus</a>
                        <button class="btn btn-sm btn-outline-info" onclick="toggleImport(<?= $program['id'] ?>)" title="Import Excel"><i class="bi bi-file-earmark-excel"></i> Import</button>
                        <a href="../export/program.php?program_id=<?= $program['id'] ?>" class="btn btn-sm btn-outline-success" target="_blank" title="Export"><i class="bi bi-download"></i> Export</a>
                        <i class="bi bi-chevron-down ms-2 collapse-icon"></i>
                    </div>
                </div>
                <div class="collapse show" id="program<?= $program['id'] ?>">
                    <div class="card-body">
                        <!-- Import Area -->
                        <div id="import-form-<?= $program['id'] ?>" style="display:none;" class="mb-3 p-3 bg-light rounded border">
                            <h6><i class="bi bi-file-earmark-excel"></i> Import Data dari Excel</h6>
                            <form method="POST" action="">
                                <?= csrf_field() ?>
                                <input type="hidden" name="program_id_import" value="<?= $program['id'] ?>">
                                <textarea name="import_data" class="form-control mb-2" rows="5" placeholder="521211	Belanja Bahan&#10;Kertas HVS	10	Rim	50000&#10;Tinta Printer	5	Botol	75000"></textarea>
                                <button type="submit" name="import_excel" class="btn btn-sm btn-success"><i class="bi bi-upload"></i> Import</button>
                            </form>
                        </div>

                        <!-- Tabel Uraian -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Uraian Kegiatan</th>
                                        <th>Volume</th>
                                        <th>Satuan</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $uraians = get_uraian_by_program($program['id']);
                                    $no_ur = 1;
                                    $total_blokir = 0;
                                    if (mysqli_num_rows($uraians) > 0):
                                        while ($uraian = mysqli_fetch_assoc($uraians)):
                                            if ($uraian['is_blokir']) $total_blokir += $uraian['nilai_blokir'];
                                    ?>
                                            <tr class="<?= get_blokir_class($uraian) ?>">
                                                <td class="text-center"><?= $no_ur++ ?></td>
                                                <td><code><?= h($uraian['kode_akun']) ?></code></td>
                                                <td><small><?= h($uraian['nama_akun']) ?></small></td>
                                                <td><?= h($uraian['uraian_kegiatan']) ?><?php if ($uraian['is_blokir']): ?><br><span class="badge bg-danger">Blokir <?= h($uraian['status_blokir']) ?>: <?= format_rupiah($uraian['nilai_blokir']) ?></span><?php endif; ?></td>
                                                <td class="text-end"><?= format_number($uraian['volume']) ?></td>
                                                <td><?= h($uraian['satuan']) ?></td>
                                                <td class="text-end"><?= format_rupiah($uraian['harga_satuan']) ?></td>
                                                <td class="text-end"><strong><?= format_rupiah($uraian['jumlah_harga']) ?></strong></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-warning" onclick="editUraian(<?= $uraian['id'] ?>, <?= $uraian['akun_id'] ?>, <?= js_escape($uraian['uraian_kegiatan']) ?>, <?= $uraian['volume'] ?>, <?= js_escape($uraian['satuan']) ?>, <?= $uraian['harga_satuan'] ?>, <?= $program['id'] ?>)" title="Edit"><i class="bi bi-pencil"></i></button>
                                                        <a href="?id=<?= $pagu_id ?>&hapus_uraian=<?= $uraian['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                                        <?php if (!$uraian['is_blokir']): ?>
                                                            <button class="btn btn-secondary" onclick="blokirUraian(<?= $uraian['id'] ?>, <?= $uraian['jumlah_harga'] ?>)" title="Blokir"><i class="bi bi-lock"></i></button>
                                                        <?php else: ?>
                                                            <a href="?id=<?= $pagu_id ?>&buka_blokir=<?= $uraian['id'] ?>" class="btn btn-success" title="Buka Blokir"><i class="bi bi-unlock"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-3"><i class="bi bi-inbox"></i> Belum ada uraian</td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($total_blokir > 0): ?>
                                        <tr class="table-info">
                                            <td colspan="7" class="text-end"><strong>Total Blokir:</strong></td>
                                            <td class="text-end"><strong><?= format_rupiah($total_blokir) ?></strong></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td colspan="7" class="text-end"><strong>Sisa Anggaran:</strong></td>
                                            <td class="text-end"><strong><?= format_rupiah($program['total_uraian'] - $total_blokir) ?></strong></td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Tambah Uraian -->
                        <div class="mt-3 p-3 bg-light rounded border">
                            <h6 class="mb-3"><i class="bi bi-plus-circle"></i> Tambah Uraian Baru</h6>
                            <form method="POST" action="">
                                <?= csrf_field() ?>
                                <input type="hidden" name="add_uraian" value="1">
                                <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-2">
                                        <select name="akun_id" class="form-select form-select-sm" required>
                                            <option value="">Pilih Akun</option>
                                            <?php mysqli_data_seek($akun_list, 0);
                                            while ($akun = mysqli_fetch_assoc($akun_list)): ?>
                                                <option value="<?= $akun['id'] ?>"><?= h($akun['kode_akun']) ?> - <?= h($akun['nama_akun']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3"><input type="text" name="uraian_kegiatan" class="form-control form-control-sm" placeholder="Uraian kegiatan" required></div>
                                    <div class="col-md-2"><input type="number" name="volume" class="form-control form-control-sm" placeholder="0" step="0.01" min="0" required></div>
                                    <div class="col-md-1"><input type="text" name="satuan" class="form-control form-control-sm" placeholder="Satuan" required></div>
                                    <div class="col-md-2"><input type="text" name="harga_satuan" class="form-control form-control-sm" placeholder="50000" required></div>
                                    <div class="col-md-2"><button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-plus"></i> Tambah</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center py-4"><i class="bi bi-info-circle fs-3 d-block mb-2"></i>
            <h5>Belum ada program</h5>
            <p>Silakan tambah program baru.</p>
        </div>
    <?php endif; ?>
</main>

<!-- Modal Edit Program -->
<div id="editProgramModal" class="modal-backdrop-custom" style="display:none;">
    <div class="modal-content-custom">
        <h5><i class="bi bi-pencil-square"></i> Edit Program</h5>
        <form method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="edit_program" value="1">
            <input type="hidden" name="program_id" id="edit_program_id">
            <div class="mb-3"><label class="form-label">Nama Program</label><input type="text" name="nama_program" id="edit_program_name" class="form-control" required></div>
            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditProgram()">Batal</button>
        </form>
    </div>
</div>

<!-- Modal Edit Uraian -->
<div id="editUraianModal" class="modal-backdrop-custom" style="display:none;">
    <div class="modal-content-custom" style="max-width:600px;">
        <h5><i class="bi bi-pencil-square"></i> Edit Uraian</h5>
        <form method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="edit_uraian" value="1">
            <input type="hidden" name="uraian_id" id="edit_uraian_id">
            <input type="hidden" name="program_id" id="edit_uraian_program_id">
            <div class="mb-2">
                <select name="akun_id" id="edit_akun_id" class="form-select">
                    <?php mysqli_data_seek($akun_list, 0);
                    while ($akun = mysqli_fetch_assoc($akun_list)): ?>
                        <option value="<?= $akun['id'] ?>"><?= h($akun['kode_akun']) ?> - <?= h($akun['nama_akun']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-2"><input type="text" name="uraian_kegiatan" id="edit_uraian_kegiatan" class="form-control" required></div>
            <div class="row g-2 mb-2">
                <div class="col-4"><input type="number" name="volume" id="edit_volume" class="form-control" step="0.01" min="0" required></div>
                <div class="col-3"><input type="text" name="satuan" id="edit_satuan" class="form-control" required></div>
                <div class="col-5"><input type="text" name="harga_satuan" id="edit_harga_satuan" class="form-control" required></div>
            </div>
            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditUraian()">Batal</button>
        </form>
    </div>
</div>

<!-- Modal Blokir -->
<div id="blokirModal" class="modal-backdrop-custom" style="display:none;">
    <div class="modal-content-custom" style="max-width:400px;">
        <h5><i class="bi bi-lock"></i> Blokir Anggaran</h5>
        <form method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="blokir_uraian" value="1">
            <input type="hidden" name="uraian_id" id="blokir_uraian_id">
            <div class="mb-3"><label class="form-label">Nilai Blokir (Rp)</label><input type="text" name="nilai_blokir" id="blokir_nilai" class="form-control" required><small id="blokir_info" class="text-muted"></small></div>
            <button type="submit" class="btn btn-danger"><i class="bi bi-lock"></i> Blokir</button>
            <button type="button" class="btn btn-secondary" onclick="closeBlokir()">Batal</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
