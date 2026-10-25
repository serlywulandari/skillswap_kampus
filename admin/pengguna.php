<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$cari = trim($_GET['cari'] ?? '');
$msg  = $_GET['msg'] ?? '';

if ($cari !== '') {
    $like = "%$cari%";
    $data = db_query(
        "SELECT * FROM pengguna
         WHERE nama_lengkap LIKE ? OR email LIKE ?
         ORDER BY id_pengguna DESC",
        "ss", $like, $like
    );
} else {
    $data = db_raw( "SELECT * FROM pengguna ORDER BY id_pengguna DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pengguna | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; }
.badge-level { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-admin { background:#e8f0fe; color:#1a3a8a; }
.badge-mahasiswa { background:#e8f5e9; color:#16a34a; }
.search-bar { display:flex; gap:10px; margin-bottom:20px; }
.search-bar input { flex:1; }
.action-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.table-wrap { overflow-x:auto; border-radius:14px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
.table-wrap table { margin:0; border-radius:0; box-shadow:none; }
.td-name { font-weight:600; color:var(--navy); }
.td-email { color:#6b7280; font-size:13px; }
.btn-sm { padding:6px 14px; font-size:13px; border-radius:8px; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">

    <div class="topbar page-header">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">Data Pengguna</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Kelola semua akun pengguna platform</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-weight:600; color:var(--navy);"><?= htmlspecialchars($_SESSION['nama']); ?></span>
        </div>
    </div>

    <?php if ($msg === 'updated'): ?>
    <div class="alert-success-bar">✅ Data pengguna berhasil diperbarui.</div>
    <?php elseif ($msg === 'added'): ?>
    <div class="alert-success-bar">✅ Pengguna baru berhasil ditambahkan.</div>
    <?php endif; ?>

    <div class="card">
        <!-- Search + Add bar -->
        <div class="action-bar">
            <form method="GET" class="search-bar" style="flex:1; margin:0;">
                <input type="text" name="cari" placeholder="🔍  Cari nama atau email..."
                       value="<?= htmlspecialchars($cari); ?>" style="max-width:380px;">
                <button class="btn btn-warning btn-sm" type="submit">Cari</button>
                <?php if ($cari): ?>
                <a href="pengguna.php" class="btn btn-primary btn-sm">Reset</a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary btn-sm"
                    onclick="document.getElementById('modalTambah').style.display='flex'">
                + Tambah Pengguna
            </button>
        </div>

        <!-- Tabel -->
        <div class="table-wrap">
        <table>
        <tr>
            <th style="width:40px;">No</th>
            <th>Nama / Email</th>
            <th>NIM</th>
            <th>Program Studi</th>
            <th>Level</th>
            <th style="width:140px;">Aksi</th>
        </tr>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td style="text-align:center; color:#9ca3af;"><?= $no++; ?></td>
            <td>
                <div class="td-name"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                <div class="td-email"><?= htmlspecialchars($row['email']); ?></div>
            </td>
            <td><?= htmlspecialchars($row['nim']); ?></td>
            <td><?= htmlspecialchars($row['program_studi'] ?: '-'); ?></td>
            <td>
                <span class="badge-level <?= $row['level'] === 'admin' ? 'badge-admin' : 'badge-mahasiswa'; ?>">
                    <?= ucfirst($row['level']); ?>
                </span>
            </td>
            <td>
                <a class="btn btn-warning btn-sm"
                   href="proses_pengguna.php?edit=<?= $row['id_pengguna']; ?>">Edit</a>
                <?php if ($row['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                <a class="btn btn-danger btn-sm"
                   onclick="return confirm('Hapus pengguna <?= htmlspecialchars(addslashes($row['nama_lengkap'])); ?>?')"
                   href="proses_pengguna.php?hapus=<?= $row['id_pengguna']; ?>">Hapus</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal" style="display:none;">
<div class="modal-content">
    <button class="modal-close" onclick="document.getElementById('modalTambah').style.display='none'">✕</button>
    <h2 style="margin-bottom:20px;">➕ Tambah Pengguna</h2>

    <form method="POST" action="proses_pengguna.php">
    <input type="hidden" name="aksi" value="tambah">

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">NIM</label>
            <input type="text" name="nim" placeholder="NIM" required>
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Email</label>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Password</label>
            <input type="password" name="kata_sandi" placeholder="Min. 6 karakter" required>
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Fakultas</label>
            <input type="text" name="fakultas" placeholder="Fakultas">
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Program Studi</label>
            <input type="text" name="program_studi" placeholder="Program Studi">
        </div>
    </div>

    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:5px;">Level</label>
    <select name="level">
        <option value="mahasiswa">Mahasiswa</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit" class="btn btn-primary" style="margin-top:16px; width:100%;">
        Simpan Pengguna
    </button>
    </form>
</div>
</div>

<script>
window.onclick = function(e) {
    const modal = document.getElementById('modalTambah');
    if (e.target === modal) modal.style.display = 'none';
}
</script>
</body>
</html>
