<?php
// login.php
require_once 'config/database.php';
require_once 'config/security.php';

if (isset($_SESSION['user_id'])) {
    safe_redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting check
    $rate_key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
    $rate = check_rate_limit($rate_key, 5, 300);

    if (!$rate['allowed']) {
        $error = 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($rate['wait'] / 60) . ' menit.';
    } else {
        $username = clean_input($_POST['username']);
        $password = $_POST['password'];

        $query = "SELECT * FROM pengguna WHERE username = '$username' AND is_active = 1 LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                // Reset rate limit on success
                unset($_SESSION[$rate_key]);

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['foto_profil'] = $user['foto_profil'] ?? 'default.png';

                safe_redirect('index.php');
            } else {
                increment_rate_limit($rate_key);
                $error = 'Username atau password salah!';
            }
        } else {
            increment_rate_limit($rate_key);
            $error = 'Username tidak ditemukan atau akun tidak aktif!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-card {
            border-radius: 1rem;
            overflow: hidden;
        }

        .login-header {
            background: var(--primary);
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-4 col-sm-8">
                <div class="card login-card shadow-lg">
                    <div class="login-header text-white">
                        <i class="bi bi-calculator fs-1 d-block mb-2"></i>
                        <h3 class="fw-bold mb-0">SIPER</h3>
                        <p class="mb-0 opacity-75">Sistem Perencanaan Keuangan</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?= h($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" autocomplete="off">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>

                        <div class="mt-4 text-center text-muted small">
                            <p class="mb-0">Default: <strong>admin</strong> / <strong>password</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
