<?php
// modules/master/templates.php

function render_master_page($tab, $error, $data_list, $edit_data, $page_title)
{
?>
    <main class="col-lg-10 ms-auto px-4 py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li class="breadcrumb-item active"><?= h($page_title) ?></li>
            </ol>
        </nav>

        <?php if ($error): echo alert('danger', $error);
        endif; ?>
        <?php if (isset($_GET['msg'])):
            $msgs = ['success' => ['success', 'Data berhasil disimpan!'], 'deleted' => ['warning', 'Data berhasil dihapus!'], 'cannot_delete' => ['danger', 'Data tidak dapat dihapus karena digunakan di ' . ($_GET['count'] ?? 0) . ' data lain!'], 'cannot_delete_admin' => ['danger', 'Admin utama tidak dapat dihapus!']];
            $m = $msgs[$_GET['msg']] ?? null;
            if ($m) echo alert($m[0], $m[1]);
        endif; ?>

        <ul class="nav nav-pills mb-4">
            <li class="nav-item"><a class="nav-link <?= $tab === 'akun' ? 'active' : '' ?>" href="?tab=akun"><i class="bi bi-journal-text"></i> Akun Belanja</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'jenis' ? 'active' : '' ?>" href="?tab=jenis"><i class="bi bi-tags"></i> Jenis Pagu</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'unit' ? 'active' : '' ?>" href="?tab=unit"><i class="bi bi-building"></i> Unit Pelaksana</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'user' ? 'active' : '' ?>" href="?tab=user"><i class="bi bi-people"></i> Pengguna</a></li>
        </ul>

        <?php
        $fn = 'render_' . $tab . '_content';
        if (function_exists($fn)) $fn($data_list, $edit_data);
        ?>
    </main>
<?php
}

function form_header($edit_data, $icon_add, $icon_edit, $title)
{
    $is_edit = !empty($edit_data);
    echo '<div class="card mb-4 shadow-sm"><div class="card-header bg-light"><h5 class="mb-0"><i class="bi bi-' . ($is_edit ? $icon_edit : $icon_add) . '"></i> ' . ($is_edit ? 'Edit' : 'Tambah') . ' ' . h($title) . '</h5></div><div class="card-body">';
}

function form_footer($edit_data, $tab)
{
    echo '<button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> ' . ($edit_data ? 'Update' : 'Simpan') . '</button>';
    if ($edit_data) echo ' <a href="?tab=' . h($tab) . '" class="btn btn-secondary">Batal</a>';
    echo '</div></div>';
}

function table_header($title, $count, $badge, $label)
{
    echo '<div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="bi bi-table"></i> ' . h($title) . '</h5><span class="badge ' . $badge . '">' . $count . ' ' . h($label) . '</span></div>';
}

function render_akun_content($data_list, $edit_data)
{
    $total = mysqli_num_rows($data_list);
    mysqli_data_seek($data_list, 0);
?>
    <form method="POST" action="" autocomplete="off">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= h($edit_data['id'] ?? '') ?>">
        <input type="hidden" name="save_akun" value="1">
        <?php form_header($edit_data, 'plus-circle', 'pencil-square', 'Akun Belanja'); ?>
        <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label">Kode Akun <span class="text-danger">*</span></label><input type="text" name="kode_akun" class="form-control" required pattern="[0-9]+" value="<?= h($edit_data['kode_akun'] ?? '') ?>"></div>
            <div class="col-md-5"><label class="form-label">Nama Akun <span class="text-danger">*</span></label><input type="text" name="nama_akun" class="form-control" required value="<?= h($edit_data['nama_akun'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" value="<?= h($edit_data['keterangan'] ?? '') ?>"></div>
        </div>
        <?php form_footer($edit_data, 'akun'); ?>
    </form>
    <div class="card shadow-sm">
        <?php table_header('Daftar Akun Belanja', $total, 'bg-primary', 'Akun'); ?>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total > 0): $no = 1;
                            while ($row = mysqli_fetch_assoc($data_list)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= h($row['kode_akun']) ?></code></td>
                                    <td><?= h($row['nama_akun']) ?></td>
                                    <td><?= h($row['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm"><a href="?tab=akun&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a><a href="?tab=akun&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus?')"><i class="bi bi-trash"></i></a></div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
}

// Fungsi render_jenis_content, render_unit_content, render_user_content
// (Disederhanakan - sama seperti di atas dengan penyesuaian field)
function render_jenis_content($data_list, $edit_data)
{
    $total = mysqli_num_rows($data_list);
    mysqli_data_seek($data_list, 0);
?>
    <form method="POST" action="" autocomplete="off">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= h($edit_data['id'] ?? '') ?>"><input type="hidden" name="save_jenis" value="1">
        <?php form_header($edit_data, 'plus-circle', 'pencil-square', 'Jenis Pagu'); ?>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><label class="form-label">Nama Jenis <span class="text-danger">*</span></label><input type="text" name="nama_jenis" class="form-control" required value="<?= h($edit_data['nama_jenis'] ?? '') ?>"></div>
            <div class="col-md-8"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" value="<?= h($edit_data['keterangan'] ?? '') ?>"></div>
        </div>
        <?php form_footer($edit_data, 'jenis'); ?>
    </form>
    <div class="card shadow-sm"><?php table_header('Daftar Jenis Pagu', $total, 'bg-info', 'Jenis'); ?><div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Jenis</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total > 0): $no = 1;
                            while ($row = mysqli_fetch_assoc($data_list)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><span class="badge bg-info fs-6"><?= h($row['nama_jenis']) ?></span></td>
                                    <td><?= h($row['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm"><a href="?tab=jenis&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a><a href="?tab=jenis&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus?')"><i class="bi bi-trash"></i></a></div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?><tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><?php
        }

        function render_unit_content($data_list, $edit_data)
        {
            $total = mysqli_num_rows($data_list);
            mysqli_data_seek($data_list, 0);
            ?>
    <form method="POST" action="" autocomplete="off">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= h($edit_data['id'] ?? '') ?>"><input type="hidden" name="save_unit" value="1">
        <?php form_header($edit_data, 'plus-circle', 'pencil-square', 'Unit Pelaksana'); ?>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><label class="form-label">Nama Unit <span class="text-danger">*</span></label><input type="text" name="nama_unit" class="form-control" required value="<?= h($edit_data['nama_unit'] ?? '') ?>"></div>
            <div class="col-md-8"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" value="<?= h($edit_data['keterangan'] ?? '') ?>"></div>
        </div>
        <?php form_footer($edit_data, 'unit'); ?>
    </form>
    <div class="card shadow-sm"><?php table_header('Daftar Unit Pelaksana', $total, 'bg-success', 'Unit'); ?><div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Unit</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total > 0): $no = 1;
                            while ($row = mysqli_fetch_assoc($data_list)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= h($row['nama_unit']) ?></strong></td>
                                    <td><?= h($row['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm"><a href="?tab=unit&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a><a href="?tab=unit&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus?')"><i class="bi bi-trash"></i></a></div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?><tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><?php
        }

        function render_user_content($data_list, $edit_data)
        {
            $total = mysqli_num_rows($data_list);
            mysqli_data_seek($data_list, 0);
            ?>
    <form method="POST" action="" autocomplete="off">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= h($edit_data['id'] ?? '') ?>"><input type="hidden" name="save_user" value="1">
        <?php form_header($edit_data, 'person-plus', 'pencil-square', 'Pengguna'); ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" name="nama_lengkap" class="form-control" required value="<?= h($edit_data['nama_lengkap'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" name="username" class="form-control" required value="<?= h($edit_data['username'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= h($edit_data['email'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Password <?= $edit_data ? '<small class="text-muted">(kosongkan jika tidak diubah)</small>' : '<span class="text-danger">*</span>' ?></label><input type="password" name="password" class="form-control" <?= !$edit_data ? 'required' : '' ?> placeholder="<?= $edit_data ? 'Kosongkan jika tidak ingin mengubah' : 'Password (min. 6 karakter)' ?>" minlength="6"><?= !$edit_data ? '<small class="text-muted">Default: password123</small>' : '' ?></div>
            <div class="col-md-4"><label class="form-label">Role <span class="text-danger">*</span></label><select name="role" class="form-select" required>
                    <option value="user" <?= ($edit_data && $edit_data['role'] === 'user') ? 'selected' : '' ?>>User (Terbatas)</option>
                    <option value="admin" <?= ($edit_data && $edit_data['role'] === 'admin') ? 'selected' : '' ?>>Admin (Penuh)</option>
                </select></div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check mb-2"><input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= (!$edit_data || $edit_data['is_active'] == 1) ? 'checked' : '' ?>><label class="form-check-label" for="is_active">Akun Aktif</label></div>
            </div>
        </div>
        <?php form_footer($edit_data, 'user'); ?>
    </form>
    <div class="card shadow-sm"><?php table_header('Daftar Pengguna', $total, 'bg-primary', 'Pengguna'); ?><div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total > 0): $no = 1;
                            while ($row = mysqli_fetch_assoc($data_list)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= h($row['nama_lengkap']) ?></strong><?= $row['id'] == 1 ? ' <i class="bi bi-shield-check text-primary" title="Admin Utama"></i>' : '' ?></td>
                                    <td><code><?= h($row['username']) ?></code></td>
                                    <td><?= h($row['email'] ?: '-') ?></td>
                                    <td><span class="badge <?= $row['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>"><?= ucfirst($row['role']) ?></span></td>
                                    <td><?= $row['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm"><a href="?tab=user&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a><?php if ($row['id'] != 1): ?><a href="?tab=user&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirmDelete('Yakin hapus?')"><i class="bi bi-trash"></i></a><?php endif; ?></div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?><tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><?php
        }
            ?>
