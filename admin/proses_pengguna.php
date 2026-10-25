<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

// TAMBAH
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama     = trim($_POST['nama_lengkap'] ?? '');
    $nim      = trim($_POST['nim'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['kata_sandi'] ?? '';
    $fakultas = trim($_POST['fakultas'] ?? '');
    $prodi    = trim($_POST['program_studi'] ?? '');
    $level    = in_array($_POST['level'], ['admin','mahasiswa']) ? $_POST['level'] : 'mahasiswa';

    $hash = password_hash($password, PASSWORD_DEFAULT);

    db_execute(
        "INSERT INTO pengguna (nama_lengkap, nim, email, kata_sandi, fakultas, program_studi, level)
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        "sssssss", $nama, $nim, $email, $hash, $fakultas, $prodi, $level
    );

    header("Location: pengguna.php?msg=added");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id !== (int)$_SESSION['id_pengguna']) {
        db_execute("DELETE FROM pengguna WHERE id_pengguna = ?", "i", $id);
    }
    header("Location: pengguna.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id       = (int)$_POST['id_pengguna'];
    $nama     = trim($_POST['nama_lengkap'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $fakultas = trim($_POST['fakultas'] ?? '');
    $prodi    = trim($_POST['program_studi'] ?? '');
    $level    = in_array($_POST['level'], ['admin','mahasiswa']) ? $_POST['level'] : 'mahasiswa';

    db_execute(
        "UPDATE pengguna SET nama_lengkap = ?, email = ?, fakultas = ?, program_studi = ?, level = ?
         WHERE id_pengguna = ?",
        "sssssi", $nama, $email, $fakultas, $prodi, $level, $id
    );

    // Update password hanya jika diisi
    $pw = trim($_POST['kata_sandi'] ?? '');
    if (!empty($pw) && strlen($pw) >= 6) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        db_execute("UPDATE pengguna SET kata_sandi = ? WHERE id_pengguna = ?", "si", $hash, $id);
    }

    header("Location: pengguna.php?msg=updated");
    exit;
}

// FORM EDIT — modern standalone page
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = db_query("SELECT * FROM pengguna WHERE id_pengguna = ?", "i", $id);
    $data = mysqli_fetch_assoc($result);
    if (!$data) { header("Location: pengguna.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Pengguna | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.edit-page {
    min-height: 100vh;
    background: var(--bg);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 40px 20px;
}
.edit-wrapper {
    width: 100%;
    max-width: 700px;
}
.edit-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 30px rgba(0,32,96,.1);
}
.edit-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}
.avatar-circle {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--navy), #1a3a8a);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 24px; font-weight: 700;
    flex-shrink: 0;
}
.edit-header h2 { font-size: 20px; color: var(--navy); margin: 0; }
.edit-header p { color: #6b7280; font-size: 13px; margin: 4px 0 0; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-group input, .form-group select {
    padding: 11px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    background: #f9fafb;
    transition: border .2s, background .2s;
    margin-bottom: 0;
}
.form-group input:focus, .form-group select:focus {
    border-color: var(--navy);
    background: white;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,32,96,.08);
}
.form-group input:disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}
.pw-hint { font-size:12px; color:#9ca3af; margin-top:3px; }
.btn-bar { display:flex; gap:12px; margin-top:28px; }
.back-link { display:inline-flex; align-items:center; gap:8px; color:var(--navy); text-decoration:none; font-weight:600; font-size:14px; margin-bottom:20px; }
.back-link:hover { color:var(--gold); }
</style>
</head>
<body>
<div class="edit-page">
<div class="edit-wrapper">
    <a href="pengguna.php" class="back-link">← Kembali ke Data Pengguna</a>

    <div class="edit-card">
        <div class="edit-header">
            <div class="avatar-circle"><?= strtoupper(substr($data['nama_lengkap'], 0, 1)); ?></div>
            <div>
                <h2>Edit Pengguna</h2>
                <p>ID #<?= $data['id_pengguna']; ?> &nbsp;·&nbsp; Terdaftar sejak <?= date('d M Y', strtotime($data['tanggal_daftar'])); ?></p>
            </div>
        </div>

        <form method="POST">
        <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna']; ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']); ?>" required>
            </div>
            <div class="form-group">
                <label>NIM</label>
                <input type="text" value="<?= htmlspecialchars($data['nim']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Level Akun</label>
                <select name="level">
                    <option value="mahasiswa" <?= $data['level']=='mahasiswa'?'selected':''; ?>>Mahasiswa</option>
                    <option value="admin" <?= $data['level']=='admin'?'selected':''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fakultas</label>
                <input type="text" name="fakultas" value="<?= htmlspecialchars($data['fakultas']); ?>" placeholder="Nama Fakultas">
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" name="program_studi" value="<?= htmlspecialchars($data['program_studi']); ?>" placeholder="Program Studi">
            </div>
            <div class="form-group full">
                <label>Password Baru <span style="color:#9ca3af;">(kosongkan jika tidak ingin mengubah)</span></label>
                <input type="password" name="kata_sandi" placeholder="Masukkan password baru (min. 6 karakter)">
                <span class="pw-hint">🔒 Password hanya akan diperbarui jika diisi minimal 6 karakter.</span>
            </div>
        </div>

        <div class="btn-bar">
            <button class="btn btn-primary" type="submit" name="update" style="flex:1;">
                💾 Simpan Perubahan
            </button>
            <a href="pengguna.php" class="btn" style="background:#f3f4f6; color:#374151;">
                Batal
            </a>
        </div>
        </form>
    </div>
</div>
</div>
</body>
</html>
<?php
}
?>
