<?php
// modules/master/user.php - Handler Pengguna
if ($tab !== 'user') return;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $nama_lengkap = clean_input($_POST['nama_lengkap']);
        $username = clean_input($_POST['username']);
        $email = clean_input($_POST['email']);
        $role = clean_input($_POST['role']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $password = $_POST['password'] ?? '';

        if (!is_unique('pengguna', 'username', $username, $_POST['id'] ?? null)) {
            $error = 'Username sudah digunakan!';
        } else {
            if (!empty($_POST['id'])) {
                $update_id = validate_int($_POST['id']);

                if (!empty($password)) {
                    if (strlen($password) < 6) {
                        $error = 'Password minimal 6 karakter!';
                    } else {
                        $hashed = password_hash($password, PASSWORD_BCRYPT);
                        mysqli_query($conn, "UPDATE pengguna SET nama_lengkap='$nama_lengkap', username='$username', email='$email', role='$role', is_active=$is_active, password='$hashed' WHERE id=$update_id");
                    }
                } else {
                    mysqli_query($conn, "UPDATE pengguna SET nama_lengkap='$nama_lengkap', username='$username', email='$email', role='$role', is_active=$is_active WHERE id=$update_id");
                }
            } else {
                if (empty($password)) $password = 'password123';
                if (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter!';
                } else {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    mysqli_query($conn, "INSERT INTO pengguna (nama_lengkap, username, password, email, role, is_active) VALUES ('$nama_lengkap', '$username', '$hashed', '$email', '$role', $is_active)");
                }
            }

            if (empty($error)) {
                safe_redirect('master_data.php?tab=user&msg=success');
            }
        }
    }
}

if ($action === 'delete' && $id > 0) {
    if ($id === 1) {
        safe_redirect('master_data.php?tab=user&msg=cannot_delete_admin');
    }

    $count = is_data_used('pagu_anggaran', 'created_by', $id);
    if ($count > 0) {
        safe_redirect("master_data.php?tab=user&msg=cannot_delete&count=$count");
    }

    mysqli_query($conn, "DELETE FROM pengguna WHERE id=$id");
    safe_redirect('master_data.php?tab=user&msg=deleted');
}

$data_list = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id ASC");
$edit_data = ($action === 'edit' && $id > 0) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengguna WHERE id=$id")) : null;
