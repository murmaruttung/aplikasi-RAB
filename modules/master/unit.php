<?php
// modules/master/unit.php - Handler Unit Pelaksana
if ($tab !== 'unit') return;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_unit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $nama_unit = clean_input($_POST['nama_unit']);
        $keterangan = clean_input($_POST['keterangan']);

        if (!is_unique('unit_pelaksana', 'nama_unit', $nama_unit, $_POST['id'] ?? null)) {
            $error = 'Nama unit sudah digunakan!';
        } else {
            if (!empty($_POST['id'])) {
                $update_id = validate_int($_POST['id']);
                mysqli_query($conn, "UPDATE unit_pelaksana SET nama_unit='$nama_unit', keterangan='$keterangan' WHERE id=$update_id");
            } else {
                mysqli_query($conn, "INSERT INTO unit_pelaksana (nama_unit, keterangan) VALUES ('$nama_unit', '$keterangan')");
            }
            safe_redirect('master_data.php?tab=unit&msg=success');
        }
    }
}

if ($action === 'delete' && $id > 0) {
    $count = is_data_used('pagu_anggaran', 'unit_id', $id);
    if ($count > 0) {
        safe_redirect("master_data.php?tab=unit&msg=cannot_delete&count=$count");
    }
    mysqli_query($conn, "DELETE FROM unit_pelaksana WHERE id=$id");
    safe_redirect('master_data.php?tab=unit&msg=deleted');
}

$data_list = mysqli_query($conn, "SELECT * FROM unit_pelaksana ORDER BY nama_unit ASC");
$edit_data = ($action === 'edit' && $id > 0) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM unit_pelaksana WHERE id=$id")) : null;
