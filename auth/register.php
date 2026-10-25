<?php
session_start();

// Jika sudah login, redirect
if (isset($_SESSION['id_pengguna'])) {
    header("Location: ../mahasiswa/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - SkillSwap Kampus</title>
<link rel="icon" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* Override grid untuk register — dua kolom tapi form lebih besar */
.register-wrapper {
    width: 100%;
    max-width: 1000px;
    display: grid;
    grid-template-columns: 1fr 520px;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}

.register-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 16px;
}

.register-form-grid .full-width {
    grid-column: 1 / -1;
}

@media (max-width: 700px) {
    .register-wrapper {
        grid-template-columns: 1fr;
    }
    .login-banner { display: none; }
    .register-form-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="auth-container" style="padding: 30px 20px;">

    <div class="register-wrapper">

        <!-- KIRI -->
        <div class="login-banner">
            <img src="../assets/img/hero.png" alt="SkillSwap Hero">
            <h2>SkillSwap Campus</h2>
            <p>
                Daftarkan diri Anda dan mulai
                perjalanan pertukaran keahlian
                bersama mahasiswa UIN Sultan Thaha
                Saifuddin Jambi.
                <br><br>
                Temukan partner belajar,
                bagikan kemampuan, dan
                bangun jaringan akademik.
            </p>
        </div>

        <!-- KANAN -->
        <div class="auth-card">

            <img src="../assets/img/logo_uin.png" alt="Logo UIN" class="auth-logo">
            <h2>Registrasi Mahasiswa</h2>

            <form method="POST" action="proses_register.php">

                <div class="register-form-grid">

                    <div class="full-width">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap"
                               placeholder="Nama lengkap sesuai KTM"
                               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>"
                               required>
                    </div>

                    <div>
                        <label>NIM</label>
                        <input type="text" name="nim"
                               placeholder="Nomor Induk Mahasiswa"
                               value="<?= htmlspecialchars($_POST['nim'] ?? ''); ?>"
                               required>
                    </div>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email"
                               placeholder="Email aktif"
                               value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="full-width">
                        <label>Password</label>
                        <input type="password" name="kata_sandi"
                               placeholder="Minimal 6 karakter"
                               required>
                    </div>

                    <div>
                        <label>Fakultas</label>
                        <input type="text" name="fakultas"
                               placeholder="Nama fakultas"
                               value="<?= htmlspecialchars($_POST['fakultas'] ?? ''); ?>">
                    </div>

                    <div>
                        <label>Program Studi</label>
                        <input type="text" name="program_studi"
                               placeholder="Nama prodi"
                               value="<?= htmlspecialchars($_POST['program_studi'] ?? ''); ?>">
                    </div>

                    <div class="full-width">
                        <button type="submit" class="btn btn-primary auth-btn"
                                style="margin-top:8px;">
                            Daftar Akun
                        </button>
                    </div>

                </div>

            </form>

            <div class="auth-link" style="margin-top:16px;">
                Sudah punya akun?
                <a href="login.php">Login Sekarang</a>
            </div>

            <div class="auth-link" style="margin-top:8px;">
                <a href="../index.php">← Kembali ke Beranda</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
