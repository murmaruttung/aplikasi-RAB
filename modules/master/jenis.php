<?php
// modules/master/jenis.php - Handler Jenis Pagu
if ($tab !== 'jenis') return;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_jenis'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $nama_jenis = clean_input($_POST['nama_jenis']);
        $keterangan = clean_input($_POST['keterangan']);

        if (!is_unique('jenis_pagu', 'nama_jenis', $nama_jenis, $_POST['id'] ?? null)) {
            $error = 'Nama jenis pagu sudah digunakan!';
        } else {
            if (!empty($_POST['id'])) {
                $update_id = validate_int($_POST['id']);
                mysqli_query($conn, "UPDATE jenis_pagu SET nama_jenis='$nama_jenis', keterangan='$keterangan' WHERE id=$update_id");
            } else {
                mysqli_query($conn, "INSERT INTO jenis_pagu (nama_jenis, keterangan) VALUES ('$nama_jenis', '$keterangan')");
            }
            safe_redirect('master_data.php?tab=jenis&msg=success');
        }
    }
}

if ($action === 'delete' && $id > 0) {
    $count = is_data_used('pagu_anggaran', 'jenis_id', $id);
    if ($count > 0) {
        safe_redirect("master_data.php?tab=jenis&msg=cannot_delete&count=$count");
    }
    mysqli_query($conn, "DELETE FROM jenis_pagu WHERE id=$id");
    safe_redirect('master_data.php?tab=jenis&msg=deleted');
}

$data_list = mysqli_query($conn, "SELECT * FROM jenis_pagu ORDER BY nama_jenis ASC");
$edit_data = ($action === 'edit' && $id > 0) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jenis_pagu WHERE id=$id")) : null;
