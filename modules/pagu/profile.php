<?php
// profile.php - Edit Profil
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/helpers.php';

check_login();

$page_title = 'Profil Saya';
$current_page = 'profile';

$user_id = get_user_id();
$user = get_current_user_data();

// Buat folder uploads jika belum ada
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

// Buat default.png jika belum ada
if (!file_exists($upload_dir . 'default.png')) {
    $img = imagecreatetruecolor(200, 200);
    $bg = imagecolorallocate($img, 200, 200, 200);
    $txt = imagecolorallocate($img, 100, 100, 100);
    imagefill($img, 0, 0, $bg);
    imagestring($img, 5, 70, 90, 'USER', $txt);
    imagepng($img, $upload_dir . 'default.png');
    imagedestroy($img);
}

$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid!';
    } else {
        $nama_lengkap = clean_input($_POST['nama_lengkap']);
        $email = clean_input($_POST['email']);
        $alamat_lengkap = clean_input($_POST['alamat_lengkap']);
        $deskripsi_diri = clean_input($_POST['deskripsi_diri']);
        $password_baru = $_POST['password_baru'] ?? '';
        $foto_profil = $user['foto_profil'];

        // Validasi password
        if (!empty($password_baru) && strlen($password_baru) < 6) {
            $error = 'Password minimal 6 karakter!';
        }

        // Handle upload foto
        if (empty($error) && isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['foto_profil']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed)) {
                $error = 'Format file tidak didukung!';
            } elseif ($_FILES['foto_profil']['size'] > 2 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 2MB!';
            } else {
                $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'][$mime];
                $new_name = 'user_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $dest = $upload_dir . $new_name;

                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $dest)) {
                    if ($foto_profil !== 'default.png' && file_exists($upload_dir . $foto_profil)) {
                        unlink($upload_dir . $foto_profil);
                    }
                    $foto_profil = $new_name;
                }
            }
        }

        // Update database
        if (empty($error)) {
            if (!empty($password_baru)) {
                $hash = password_hash($password_baru, PASSWORD_BCRYPT);
                mysqli_query($conn, "UPDATE pengguna SET nama_lengkap='$nama_lengkap', email='$email', alamat_lengkap='$alamat_lengkap', deskripsi_diri='$deskripsi_diri', foto_profil='$foto_profil', password='$hash', updated_at=NOW() WHERE id=$user_id");
            } else {
                mysqli_query($conn, "UPDATE pengguna SET nama_lengkap='$nama_lengkap', email='$email', alamat_lengkap='$alamat_lengkap', deskripsi_diri='$deskripsi_diri', foto_profil='$foto_profil', updated_at=NOW() WHERE id=$user_id");
            }

            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['foto_profil'] = $foto_profil;
            $msg = 'Profil berhasil diperbarui!';
            $user = get_current_user_data();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<main class="col-lg-10 ms-auto px-4 py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Profil Saya</li>
        </ol>
    </nav>

    <?php if ($msg): echo alert('success', $msg);
    endif; ?>
    <?php if ($error): echo alert('danger', $error);
    endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="uploads/<?= h($user['foto_profil'] ?? 'default.png') ?>" alt="Foto" class="rounded-circle mb-3" width="150" height="150" style="object-fit:cover;border:3px solid var(--primary)" onerror="this.src='uploads/default.png'">
                    <h5><?= h($user['nama_lengkap']) ?></h5>
                    <p class="text-muted mb-1">@<?= h($user['username']) ?></p>
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?> mb-2"><?= ucfirst(h($user['role'])) ?></span>
                    <?php if ($user['email']): ?><p class="small text-muted"><i class="bi bi-envelope"></i> <?= h($user['email']) ?></p><?php endif; ?>
                    <?php if ($user['alamat_lengkap']): ?><p class="small text-muted"><i class="bi bi-geo-alt"></i> <?= h($user['alamat_lengkap']) ?></p><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="row g-3 mb-3">
                            <div class="col-12"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" name="nama_lengkap" class="form-control" required value="<?= h($user['nama_lengkap']) ?>"></div>
                            <div class="col-md-6"><label class="form-label">Username</label><input type="text" class="form-control bg-light" value="<?= h($user['username']) ?>" disabled><small class="text-muted">Username tidak dapat diubah</small></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= h($user['email'] ?? '') ?>"></div>
                            <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat_lengkap" class="form-control" rows="2"><?= h($user['alamat_lengkap'] ?? '') ?></textarea></div>
                            <div class="col-12"><label class="form-label">Deskripsi Diri</label><textarea name="deskripsi_diri" class="form-control" rows="3"><?= h($user['deskripsi_diri'] ?? '') ?></textarea></div>
                            <div class="col-12"><label class="form-label">Foto Profil</label><input type="file" name="foto_profil" class="form-control" accept="image/*"><small class="text-muted">Format: JPG, PNG, GIF. Maks 2MB.</small></div>
                        </div>
                        <hr>
                        <h6 class="mb-3" id="password"><i class="bi bi-key"></i> Ubah Password</h6>
                        <div class="mb-3"><label class="form-label">Password Baru</label><input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="6"></div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
