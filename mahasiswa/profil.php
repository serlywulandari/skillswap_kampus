<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id = (int)$_SESSION['id_pengguna'];
$result = db_query("SELECT * FROM pengguna WHERE id_pengguna = ?", "i", $id);
$data = mysqli_fetch_assoc($result);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.profil-wrap { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }
.profil-card-left {
    background: white;
    border-radius: 20px;
    padding: 32px 24px;
    box-shadow: 0 4px 20px rgba(0,32,96,.08);
    text-align: center;
}
.avatar-big {
    width: 90px; height: 90px;
    background: linear-gradient(135deg, var(--navy), #1a3a8a);
    border-radius: 24px;
    display: flex; align-items: center; justify-content: center;
    font-size: 38px; font-weight: 800; color: white;
    margin: 0 auto 16px;
}
.profil-name { font-size: 18px; font-weight: 700; color: var(--navy); margin-bottom: 4px; }
.profil-nim { font-size: 13px; color: #9ca3af; margin-bottom: 12px; }
.profil-badge { display:inline-block; background:#e8f5e9; color:#16a34a; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600; }
.info-row { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f3f4f6; }
.info-row:last-child { border-bottom: none; }
.info-icon { font-size:16px; width:20px; }
.info-text { font-size:13px; color:#6b7280; }
.info-val { font-size:14px; color:var(--navy); font-weight:500; margin-top:1px; }
.form-card {
    background: white;
    border-radius: 20px;
    padding: 36px;
    box-shadow: 0 4px 20px rgba(0,32,96,.08);
}
.form-card h3 { font-size:18px; color:var(--navy); margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid #f0f0f0; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1/-1; }
.form-group label { font-size:13px; font-weight:600; color:#374151; }
.form-group input {
    padding: 11px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    background: #f9fafb;
    margin-bottom: 0;
    transition: border .2s;
}
.form-group input:focus { border-color:var(--navy); background:white; outline:none; box-shadow:0 0 0 3px rgba(0,32,96,.08); }
.form-group input:disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; }
.section-divider { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; grid-column:1/-1; margin-top:8px; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">Profil Saya</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Kelola informasi akun Anda</p>
        </div>
    </div>

    <?php if ($msg === 'updated'): ?>
    <div class="alert-success-bar">✅ Profil berhasil diperbarui.</div>
    <?php endif; ?>

    <div class="profil-wrap">
        <!-- Kartu kiri: info ringkas -->
        <div class="profil-card-left">
            <div class="avatar-big"><?= strtoupper(substr($data['nama_lengkap'], 0, 1)); ?></div>
            <div class="profil-name"><?= htmlspecialchars($data['nama_lengkap']); ?></div>
            <div class="profil-nim"><?= htmlspecialchars($data['nim']); ?></div>
            <span class="profil-badge">Mahasiswa</span>

            <div style="margin-top:24px; text-align:left;">
                <div class="info-row">
                    <span class="info-icon">📧</span>
                    <div><div class="info-text">Email</div><div class="info-val"><?= htmlspecialchars($data['email']); ?></div></div>
                </div>
                <div class="info-row">
                    <span class="info-icon">🏛️</span>
                    <div><div class="info-text">Fakultas</div><div class="info-val"><?= htmlspecialchars($data['fakultas'] ?: '-'); ?></div></div>
                </div>
                <div class="info-row">
                    <span class="info-icon">📖</span>
                    <div><div class="info-text">Program Studi</div><div class="info-val"><?= htmlspecialchars($data['program_studi'] ?: '-'); ?></div></div>
                </div>
                <div class="info-row">
                    <span class="info-icon">📅</span>
                    <div><div class="info-text">Bergabung</div><div class="info-val"><?= date('d M Y', strtotime($data['tanggal_daftar'])); ?></div></div>
                </div>
            </div>
        </div>

        <!-- Form edit -->
        <div class="form-card">
            <h3>✏️ Edit Informasi Profil</h3>
            <form method="POST" action="proses_profil.php">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']); ?>" required>
                </div>
                <div class="form-group">
                    <label>NIM <span style="color:#9ca3af;">(tidak dapat diubah)</span></label>
                    <input type="text" value="<?= htmlspecialchars($data['nim']); ?>" disabled>
                </div>
                <div class="form-group full">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Fakultas</label>
                    <input type="text" name="fakultas" value="<?= htmlspecialchars($data['fakultas']); ?>" placeholder="Nama Fakultas">
                </div>
                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" name="program_studi" value="<?= htmlspecialchars($data['program_studi']); ?>" placeholder="Program Studi">
                </div>

                <div class="section-divider">🔐 Ubah Password</div>
                <div class="form-group full">
                    <label>Password Baru <span style="color:#9ca3af;">(kosongkan jika tidak ingin mengubah)</span></label>
                    <input type="password" name="password_baru" placeholder="Masukkan password baru (min. 6 karakter)">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:24px; padding:14px;">
                💾 Simpan Perubahan
            </button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
