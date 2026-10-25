<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id = (int)$_SESSION['id_pengguna'];

$data = db_query(
    "SELECT jb.*, pp.status,
            u1.nama_lengkap AS pengirim, u2.nama_lengkap AS penerima
     FROM jadwal_belajar jb
     JOIN permintaan_pertukaran pp ON jb.id_pertukaran = pp.id_pertukaran
     JOIN pengguna u1 ON pp.pengirim = u1.id_pengguna
     JOIN pengguna u2 ON pp.penerima = u2.id_pengguna
     WHERE pp.pengirim = ? OR pp.penerima = ?
     ORDER BY jb.tanggal ASC, jb.jam ASC",
    "ii", $id, $id
);
$rows = [];
while ($r = mysqli_fetch_assoc($data)) $rows[] = $r;

// Pisahkan jadwal yang akan datang dan yang sudah lewat
$upcoming = [];
$past = [];
$today = date('Y-m-d');
foreach ($rows as $r) {
    if ($r['tanggal'] >= $today) $upcoming[] = $r;
    else $past[] = $r;
}
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
    padding: 20px 22px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(0,32,96,.07);
    border-left: 4px solid var(--navy);
    display: flex;
    align-items: center;
    gap: 18px;
}
.jadwal-card.past { border-left-color: #d1d5db; opacity: .75; }
.jadwal-date {
    background: var(--navy);
    color: white;
    border-radius: 12px;
    padding: 10px 14px;
    text-align: center;
    min-width: 60px;
    flex-shrink: 0;
}
.jadwal-card.past .jadwal-date { background: #9ca3af; }
.jadwal-date .day  { font-size: 22px; font-weight: 800; line-height: 1; }
.jadwal-date .mon  { font-size: 11px; opacity: .8; text-transform: uppercase; }
.jadwal-info { flex: 1; }
.jadwal-partner { font-weight: 700; color: var(--navy); font-size: 15px; }
.jadwal-you { font-size: 12px; color: var(--gold); font-weight: 600; }
.jadwal-meta { font-size: 13px; color: #6b7280; margin-top: 5px; }
.jadwal-loc { display:inline-flex; align-items:center; gap:5px; background:#f0f4ff; color:var(--navy); padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; margin-top:6px; }
.section-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin:20px 0 12px; }
.empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
.badge-upcoming { background:#e8f5e9; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-past     { background:#f3f4f6; color:#9ca3af; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">📅 Jadwal Belajar</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Sesi belajar yang dijadwalkan untuk Anda</p>
        </div>
        <span style="background:var(--gold); color:var(--navy); padding:5px 16px; border-radius:20px; font-size:13px; font-weight:700;">
            <?= count($rows); ?> Sesi
        </span>
    </div>

    <?php if (empty($rows)): ?>
    <div class="card">
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:12px;">📭</div>
            <p style="font-size:16px; font-weight:600; color:var(--navy);">Belum ada jadwal belajar</p>
            <p style="font-size:14px; max-width:340px; margin:0 auto;">
                Jadwal akan dibuat oleh admin setelah permintaan pertukaran Anda diterima.
                Pastikan Anda sudah mengajukan dan menerima permintaan pertukaran terlebih dahulu.
            </p>
            <a href="cari_partner.php" class="btn btn-primary" style="margin-top:20px; display:inline-block;">
                🔍 Cari Partner Sekarang
            </a>
        </div>
    </div>
    <?php else: ?>

    <?php if (!empty($upcoming)): ?>
    <div class="section-label">🔜 Jadwal Mendatang (<?= count($upcoming); ?>)</div>
    <?php foreach ($upcoming as $r):
        $partner = ($r['pengirim'] === $_SESSION['nama']) ? $r['penerima'] : $r['pengirim'];
    ?>
    <div class="jadwal-card">
        <div class="jadwal-date">
            <div class="day"><?= date('d', strtotime($r['tanggal'])); ?></div>
            <div class="mon"><?= date('M y', strtotime($r['tanggal'])); ?></div>
        </div>
        <div class="jadwal-info">
            <div class="jadwal-partner">🤝 Sesi dengan <?= htmlspecialchars($partner); ?></div>
            <div class="jadwal-meta">🕐 <?= date('H:i', strtotime($r['jam'])); ?> WIB</div>
            <?php if ($r['catatan']): ?>
            <div class="jadwal-meta">📝 <?= htmlspecialchars($r['catatan']); ?></div>
            <?php endif; ?>
            <span class="jadwal-loc">📍 <?= htmlspecialchars($r['lokasi']); ?></span>
        </div>
        <span class="badge-upcoming">Mendatang</span>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($past)): ?>
    <div class="section-label">📂 Riwayat Sesi (<?= count($past); ?>)</div>
    <?php foreach ($past as $r):
        $partner = ($r['pengirim'] === $_SESSION['nama']) ? $r['penerima'] : $r['pengirim'];
    ?>
    <div class="jadwal-card past">
        <div class="jadwal-date">
            <div class="day"><?= date('d', strtotime($r['tanggal'])); ?></div>
            <div class="mon"><?= date('M y', strtotime($r['tanggal'])); ?></div>
        </div>
        <div class="jadwal-info">
            <div class="jadwal-partner" style="color:#6b7280;">🤝 Sesi dengan <?= htmlspecialchars($partner); ?></div>
            <div class="jadwal-meta">🕐 <?= date('H:i', strtotime($r['jam'])); ?> WIB</div>
            <?php if ($r['catatan']): ?>
            <div class="jadwal-meta">📝 <?= htmlspecialchars($r['catatan']); ?></div>
            <?php endif; ?>
            <span class="jadwal-loc" style="background:#f3f4f6; color:#6b7280;">📍 <?= htmlspecialchars($r['lokasi']); ?></span>
        </div>
        <span class="badge-past">Selesai</span>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php endif; ?>
</div>
</body>
</html>
