<?php
// modules/master/akun.php
if ($tab !== 'akun') return;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_akun'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $kode_akun = clean_input($_POST['kode_akun']);
        $nama_akun = clean_input($_POST['nama_akun']);
        $keterangan = clean_input($_POST['keterangan']);

        if (!is_unique('akun_belanja', 'kode_akun', $kode_akun, $_POST['id'] ?? null)) {
            $error = 'Kode akun sudah digunakan!';
        } else {
            if (!empty($_POST['id'])) {
                $update_id = validate_int($_POST['id']);
                mysqli_query($conn, "UPDATE akun_belanja SET kode_akun='$kode_akun', nama_akun='$nama_akun', keterangan='$keterangan' WHERE id=$update_id");
            } else {
                mysqli_query($conn, "INSERT INTO akun_belanja (kode_akun, nama_akun, keterangan) VALUES ('$kode_akun', '$nama_akun', '$keterangan')");
            }
            safe_redirect('master_data.php?tab=akun&msg=success');
        }
    }
}

if ($action === 'delete' && $id > 0) {
    $count = is_data_used('uraian_anggaran', 'akun_id', $id);
    if ($count > 0) {
        safe_redirect("master_data.php?tab=akun&msg=cannot_delete&count=$count");
    }
    mysqli_query($conn, "DELETE FROM akun_belanja WHERE id=$id");
    safe_redirect('master_data.php?tab=akun&msg=deleted');
}

$data_list = mysqli_query($conn, "SELECT * FROM akun_belanja ORDER BY kode_akun ASC");
$edit_data = ($action === 'edit' && $id > 0) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM akun_belanja WHERE id=$id")) : null;
