<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

// TAMBAH
if (isset($_POST['aksi'])) {
    $nama = trim($_POST['nama_keahlian'] ?? '');
    if (!empty($nama)) {
        db_execute("INSERT INTO keahlian (nama_keahlian) VALUES (?)", "s", $nama);
    }
    header("Location: keahlian.php?msg=added");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    db_execute("DELETE FROM keahlian WHERE id_keahlian = ?", "i", $id);
    header("Location: keahlian.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id   = (int)$_POST['id_keahlian'];
    $nama = trim($_POST['nama_keahlian'] ?? '');
    if (!empty($nama)) {
        db_execute(
            "UPDATE keahlian SET nama_keahlian = ? WHERE id_keahlian = ?",
            "si", $nama, $id
        );
    }
    header("Location: keahlian.php?msg=updated");
    exit;
}

// FORM EDIT — modern standalone
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = db_query("SELECT * FROM keahlian WHERE id_keahlian = ?", "i", $id);
    $data = mysqli_fetch_assoc($result);
    if (!$data) { header("Location: keahlian.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Keahlian | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.edit-page {
    min-height: 100vh;
    background: var(--bg);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}
.edit-wrapper { width: 100%; max-width: 520px; }
.edit-card {
    background: white;
    border-radius: 20px;
    padding: 44px 40px;
    box-shadow: 0 8px 30px rgba(0,32,96,.1);
}
.icon-big {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #e8f0fe, #d1e3ff);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 30px;
    margin: 0 auto 20px;
}
.edit-card h2 { text-align:center; color:var(--navy); font-size:22px; margin-bottom:6px; }
.edit-card .sub { text-align:center; color:#6b7280; font-size:14px; margin-bottom:28px; }
.form-label { font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px; }
.input-styled {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    font-size: 15px;
    background: #f9fafb;
    transition: border .2s;
    margin-bottom: 20px;
}
.input-styled:focus {
    border-color: var(--navy);
    background: white;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,32,96,.08);
}
.btn-bar { display:flex; gap:12px; }
.back-link {
    display: inline-flex; align-items: center; gap: 8px;
    color: var(--navy); text-decoration: none;
    font-weight: 600; font-size: 14px; margin-bottom: 20px;
}
.back-link:hover { color: var(--gold); }
</style>
</head>
<body>
<div class="edit-page">
<div class="edit-wrapper">
    <a href="keahlian.php" class="back-link">← Kembali ke Data Keahlian</a>

    <div class="edit-card">
        <div class="icon-big">🎯</div>
        <h2>Edit Keahlian</h2>
        <p class="sub">ID Keahlian: #<?= $data['id_keahlian']; ?></p>

        <form method="POST">
        <input type="hidden" name="id_keahlian" value="<?= $data['id_keahlian']; ?>">
        <label class="form-label">Nama Keahlian</label>
        <input type="text" name="nama_keahlian" class="input-styled"
               value="<?= htmlspecialchars($data['nama_keahlian']); ?>" required>

        <div class="btn-bar">
            <button class="btn btn-primary" name="update" style="flex:1;">💾 Simpan Perubahan</button>
            <a href="keahlian.php" class="btn" style="background:#f3f4f6; color:#374151;">Batal</a>
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
