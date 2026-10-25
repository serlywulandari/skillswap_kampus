<?php
session_start();
include '../config/koneksi.php';

// Jika sudah login, redirect langsung
if (isset($_SESSION['id_pengguna'])) {
    if ($_SESSION['level'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../mahasiswa/dashboard.php");
    }
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        // Ambil user berdasarkan email
        $result = db_query(
            "SELECT * FROM pengguna WHERE email = ? LIMIT 1",
            "s", $email
        );
        $data = $result ? mysqli_fetch_assoc($result) : null;

        $valid = false;
        if ($data) {
            if (password_verify($password, $data['kata_sandi'])) {
                // Password sudah di-hash
                $valid = true;
            } elseif ($data['kata_sandi'] === $password) {
                // Password lama plaintext — upgrade ke hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                db_execute(
                    "UPDATE pengguna SET kata_sandi = ? WHERE id_pengguna = ?",
                    "si", $hash, $data['id_pengguna']
                );
                $valid = true;
            }
        }

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['id_pengguna'] = $data['id_pengguna'];
            $_SESSION['nama']        = $data['nama_lengkap'];
            $_SESSION['level']       = $data['level'];

            if ($data['level'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../mahasiswa/dashboard.php");
            }
            exit;
        } else {
            $error = "Email atau Password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - SkillSwap Campus</title>
<link rel="icon" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="auth-container">

    <div class="login-wrapper">

        <!-- BAGIAN KIRI -->
        <div class="login-banner">
            <img src="../assets/img/hero.png" alt="SkillSwap Hero">
            <h2>SkillSwap Campus</h2>
            <p>
                Platform pertukaran keahlian mahasiswa
                UIN Sultan Thaha Saifuddin Jambi.
                <br><br>
                Temukan partner belajar,
                berbagi kemampuan,
                dan bangun kolaborasi akademik
                dengan mahasiswa lainnya.
            </p>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="auth-card">

            <img src="../assets/img/logo_uin.png" alt="Logo UIN" class="auth-logo">
            <h2>Login Sistem</h2>

            <?php if ($error != ''): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST">

                <label>Email</label>
                <input type="email" name="email"
                       placeholder="Masukkan Email"
                       value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                       required>

                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Masukkan Password"
                       required>

                <button type="submit" name="login"
                        class="btn btn-primary auth-btn">
                    Login
                </button>

            </form>

            <div class="auth-link">
                Belum punya akun?
                <a href="register.php">Daftar Sekarang</a>
            </div>

            <div class="auth-link" style="margin-top:10px;">
                <a href="../index.php">← Kembali ke Beranda</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
