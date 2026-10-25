<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$msg = $_GET['msg'] ?? '';

$data = db_raw( "SELECT * FROM keahlian ORDER BY nama_keahlian ASC");
$total = mysqli_num_rows($data);
$data = db_raw( "SELECT * FROM keahlian ORDER BY nama_keahlian ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Keahlian | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.skill-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
    margin-top: 20px;
}
.skill-item {
    background: white;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    transition: border .2s, box-shadow .2s;
}
.skill-item:hover { border-color: var(--gold); box-shadow: 0 4px 14px rgba(0,32,96,.08); }
.skill-name { font-weight: 600; color: var(--navy); font-size: 15px; }
.skill-no { font-size: 12px; color: #9ca3af; margin-top: 2px; }
.skill-icon { font-size: 22px; }
.skill-actions { display: flex; gap: 8px; flex-shrink: 0; }
.btn-xs { padding: 5px 12px; font-size: 12px; border-radius: 8px; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
.count-badge { background:var(--gold); color:var(--navy); padding:4px 14px; border-radius:20px; font-size:13px; font-weight:700; }
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">Data Keahlian</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Kelola daftar keahlian yang tersedia</p>
        </div>
        <span class="count-badge"><?= $total; ?> Keahlian</span>
    </div>

    <?php if ($msg === 'added'): ?>
    <div class="alert-success-bar">✅ Keahlian baru berhasil ditambahkan.</div>
    <?php elseif ($msg === 'updated'): ?>
    <div class="alert-success-bar">✅ Keahlian berhasil diperbarui.</div>
    <?php endif; ?>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <p style="color:#6b7280; font-size:14px; margin:0;">Klik tombol tambah untuk menambah keahlian baru</p>
            <button class="btn btn-primary btn-xs"
                    onclick="document.getElementById('modalTambah').style.display='flex'">
                + Tambah Keahlian
            </button>
        </div>

        <div class="skill-grid">
        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
        <div class="skill-item">
            <div>
                <div class="skill-no">#<?= $no++; ?></div>
                <div class="skill-name"><?= htmlspecialchars($row['nama_keahlian']); ?></div>
            </div>
            <div class="skill-actions">
                <a href="proses_keahlian.php?edit=<?= $row['id_keahlian']; ?>"
                   class="btn btn-warning btn-xs">Edit</a>
                <a href="proses_keahlian.php?hapus=<?= $row['id_keahlian']; ?>"
                   class="btn btn-danger btn-xs"
                   onclick="return confirm('Hapus keahlian <?= htmlspecialchars(addslashes($row['nama_keahlian'])); ?>?')">Hapus</a>
            </div>
        </div>
        <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal" style="display:none;">
<div class="modal-content">
    <button class="modal-close" onclick="document.getElementById('modalTambah').style.display='none'">✕</button>
    <h2>➕ Tambah Keahlian</h2>
    <p style="color:#6b7280; font-size:14px; margin:-10px 0 20px;">Masukkan nama keahlian baru yang akan ditambahkan ke sistem.</p>

    <form method="POST" action="proses_keahlian.php">
    <input type="hidden" name="aksi" value="tambah">
    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Nama Keahlian</label>
    <input type="text" name="nama_keahlian" placeholder="Contoh: Python, Desain Grafis..." required style="margin-bottom:16px;">
    <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Keahlian</button>
    </form>
</div>
</div>

<script>
window.onclick = function(e) {
    const m = document.getElementById('modalTambah');
    if (e.target === m) m.style.display = 'none';
}
</script>
</body>
</html>
