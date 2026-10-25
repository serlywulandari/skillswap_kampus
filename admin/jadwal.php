<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$msg = $_GET['msg'] ?? '';

// Ambil pertukaran yang sudah Diterima untuk dropdown
$pertukaran_diterima = db_raw(
    "SELECT p.id_pertukaran, u1.nama_lengkap AS pengirim, u2.nama_lengkap AS penerima
     FROM permintaan_pertukaran p
     JOIN pengguna u1 ON p.pengirim = u1.id_pengguna
     JOIN pengguna u2 ON p.penerima = u2.id_pengguna
     WHERE p.status = 'Diterima'
     ORDER BY p.id_pertukaran DESC"
);

$data = db_raw(
    "SELECT j.*, u1.nama_lengkap AS pengirim, u2.nama_lengkap AS penerima
     FROM jadwal_belajar j
     JOIN permintaan_pertukaran p ON j.id_pertukaran = p.id_pertukaran
     JOIN pengguna u1 ON p.pengirim = u1.id_pengguna
     JOIN pengguna u2 ON p.penerima = u2.id_pengguna
     ORDER BY j.tanggal ASC, j.jam ASC"
);
$rows = [];
while ($r = mysqli_fetch_assoc($data)) $rows[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Belajar | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.jadwal-card {
    background: white;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    border-left: 4px solid var(--navy);
    display: flex;
    align-items: center;
    gap: 20px;
}
.jadwal-date {
    background: var(--navy);
    color: white;
    border-radius: 12px;
    padding: 10px 14px;
    text-align: center;
    min-width: 62px;
    flex-shrink: 0;
}
.jadwal-date .day { font-size: 22px; font-weight: 800; line-height: 1; }
.jadwal-date .mon { font-size: 11px; opacity: .8; text-transform: uppercase; }
.jadwal-info { flex: 1; }
.jadwal-partner { font-weight: 700; color: var(--navy); font-size: 15px; }
.jadwal-meta { font-size: 13px; color: #6b7280; margin-top: 4px; }
.jadwal-loc { display: inline-flex; align-items: center; gap: 5px; background: #f0f4ff; color: var(--navy); padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 6px; }
.btn-sm { padding: 7px 14px; font-size: 13px; border-radius: 8px; }
.empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">📅 Jadwal Belajar</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Kelola sesi belajar antar mahasiswa</p>
        </div>
        <button class="btn btn-primary btn-sm"
                onclick="document.getElementById('modalTambah').style.display='flex'">
            + Tambah Jadwal
        </button>
    </div>

    <?php if ($msg === 'added'): ?>
    <div class="alert-success-bar">✅ Jadwal belajar berhasil ditambahkan.</div>
    <?php elseif ($msg === 'deleted'): ?>
    <div class="alert-success-bar">🗑️ Jadwal berhasil dihapus.</div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
    <div class="card">
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:12px;">📭</div>
            <p style="font-size:16px; font-weight:600; color:var(--navy);">Belum ada jadwal belajar</p>
            <p style="font-size:14px;">Klik "+ Tambah Jadwal" untuk membuat sesi belajar baru.<br>
            Pastikan ada permintaan pertukaran yang sudah berstatus <strong>Diterima</strong>.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <p style="color:#6b7280; font-size:14px; margin-bottom:18px;">Total <strong><?= count($rows); ?></strong> jadwal terdaftar</p>
        <?php foreach ($rows as $r): ?>
        <div class="jadwal-card">
            <div class="jadwal-date">
                <div class="day"><?= date('d', strtotime($r['tanggal'])); ?></div>
                <div class="mon"><?= date('M Y', strtotime($r['tanggal'])); ?></div>
            </div>
            <div class="jadwal-info">
                <div class="jadwal-partner">
                    👤 <?= htmlspecialchars($r['pengirim']); ?> &nbsp;↔&nbsp; <?= htmlspecialchars($r['penerima']); ?>
                </div>
                <div class="jadwal-meta">
                    🕐 <?= date('H:i', strtotime($r['jam'])); ?> WIB
                    <?php if ($r['catatan']): ?>
                    &nbsp;·&nbsp; 📝 <?= htmlspecialchars($r['catatan']); ?>
                    <?php endif; ?>
                </div>
                <span class="jadwal-loc">📍 <?= htmlspecialchars($r['lokasi']); ?></span>
            </div>
            <a href="proses_jadwal.php?hapus=<?= $r['id_jadwal']; ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Jadwal -->
<div id="modalTambah" class="modal" style="display:none;">
<div class="modal-content" style="max-width:520px;">
    <button class="modal-close" onclick="document.getElementById('modalTambah').style.display='none'">✕</button>
    <h2>📅 Tambah Jadwal Belajar</h2>
    <p style="color:#6b7280; font-size:14px; margin:-10px 0 20px;">Hanya pertukaran berstatus <strong>Diterima</strong> yang bisa dijadwalkan.</p>

    <form method="POST" action="proses_jadwal.php">
    <input type="hidden" name="aksi" value="tambah">

    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Pasangan Pertukaran</label>
    <select name="id_pertukaran" required style="margin-bottom:14px;">
        <option value="">-- Pilih Pasangan --</option>
        <?php
        $rows_p = [];
        while ($p = mysqli_fetch_assoc($pertukaran_diterima)) $rows_p[] = $p;
        foreach ($rows_p as $p):
        ?>
        <option value="<?= $p['id_pertukaran']; ?>">
            #<?= $p['id_pertukaran']; ?> — <?= htmlspecialchars($p['pengirim']); ?> ↔ <?= htmlspecialchars($p['penerima']); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Tanggal</label>
            <input type="date" name="tanggal" required style="margin-bottom:0;">
        </div>
        <div>
            <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Jam</label>
            <input type="time" name="jam" required style="margin-bottom:0;">
        </div>
    </div>

    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin:14px 0 6px;">Lokasi</label>
    <input type="text" name="lokasi" placeholder="Contoh: Perpustakaan, Lab Komputer, Zoom..." required style="margin-bottom:14px;">

    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Catatan <span style="color:#9ca3af;">(opsional)</span></label>
    <textarea name="catatan" placeholder="Catatan sesi belajar..." rows="3" style="margin-bottom:16px;"></textarea>

    <button type="submit" class="btn btn-primary" style="width:100%;">💾 Simpan Jadwal</button>
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
