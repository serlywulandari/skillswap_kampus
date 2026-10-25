<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id = (int)$_SESSION['id_pengguna'];

// Terima permintaan
if (isset($_GET['terima'])) {
    $id_pertukaran = (int)$_GET['terima'];
    db_execute(
        "UPDATE permintaan_pertukaran SET status='Diterima' WHERE id_pertukaran=? AND penerima=?",
        "ii", $id_pertukaran, $id
    );
    header("Location: notifikasi.php?msg=diterima"); exit;
}

// Tolak permintaan
if (isset($_GET['tolak'])) {
    $id_pertukaran = (int)$_GET['tolak'];
    db_execute(
        "UPDATE permintaan_pertukaran SET status='Ditolak' WHERE id_pertukaran=? AND penerima=?",
        "ii", $id_pertukaran, $id
    );
    header("Location: notifikasi.php?msg=ditolak"); exit;
}

$msg = $_GET['msg'] ?? '';

$data = db_query(
    "SELECT p.id_pertukaran, u.nama_lengkap, u.program_studi, p.status, p.pesan, p.tanggal_pengajuan
     FROM permintaan_pertukaran p
     JOIN pengguna u ON p.pengirim = u.id_pengguna
     WHERE p.penerima = ?
     ORDER BY p.id_pertukaran DESC",
    "i", $id
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifikasi | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.notif-item {
    background: white;
    border-radius: 16px;
    padding: 22px 24px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    border-left: 4px solid #e5e7eb;
    display: flex;
    align-items: flex-start;
    gap: 16px;
}
.notif-item.menunggu { border-left-color: #f59e0b; }
.notif-item.diterima { border-left-color: #16a34a; }
.notif-item.ditolak  { border-left-color: #dc2626; }
.notif-avatar {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, var(--navy), #1a3a8a);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: 18px;
    flex-shrink: 0;
}
.notif-body { flex: 1; }
.notif-name { font-weight: 700; color: var(--navy); font-size: 15px; }
.notif-prodi { font-size: 12px; color: #9ca3af; }
.notif-pesan { font-size: 13px; color: #6b7280; margin: 6px 0; }
.notif-date { font-size: 12px; color: #9ca3af; }
.notif-actions { display: flex; gap: 8px; margin-top: 10px; }
.badge-menunggu { background:#fff8e1; color:#b45309; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-diterima { background:#e8f5e9; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-ditolak  { background:#fee2e2; color:#dc2626; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
.btn-sm { padding:7px 16px; font-size:13px; border-radius:9px; }
.alert-bar { padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; }
.alert-danger-bar2  { background:#fee2e2; color:#dc2626; border:1px solid #f5c6cb; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">🔔 Notifikasi Pertukaran</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Permintaan pertukaran skill yang masuk ke Anda</p>
        </div>
    </div>

    <?php if ($msg === 'diterima'): ?>
    <div class="alert-bar alert-success-bar">✅ Permintaan berhasil diterima! Anda kini bisa menjadwalkan sesi belajar.</div>
    <?php elseif ($msg === 'ditolak'): ?>
    <div class="alert-bar alert-danger-bar2">❌ Permintaan telah ditolak.</div>
    <?php endif; ?>

    <?php
    $rows = [];
    while ($row = mysqli_fetch_assoc($data)) $rows[] = $row;
    if (empty($rows)):
    ?>
    <div style="text-align:center; padding:60px 20px; color:#9ca3af;">
        <div style="font-size:48px; margin-bottom:12px;">📭</div>
        <p style="font-size:16px; font-weight:600;">Belum ada notifikasi</p>
        <p style="font-size:14px;">Permintaan pertukaran skill akan muncul di sini</p>
    </div>
    <?php else: foreach ($rows as $row): ?>
    <div class="notif-item <?= strtolower($row['status']); ?>">
        <div class="notif-avatar"><?= strtoupper(substr($row['nama_lengkap'], 0, 1)); ?></div>
        <div class="notif-body">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div class="notif-name"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                    <div class="notif-prodi"><?= htmlspecialchars($row['program_studi']); ?></div>
                </div>
                <span class="badge-<?= strtolower($row['status']); ?>"><?= $row['status']; ?></span>
            </div>
            <div class="notif-pesan">💬 <?= htmlspecialchars($row['pesan']); ?></div>
            <div class="notif-date">🕐 <?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?></div>
            <?php if ($row['status'] === 'Menunggu'): ?>
            <div class="notif-actions">
                <a href="notifikasi.php?terima=<?= $row['id_pertukaran']; ?>"
                   class="btn btn-success btn-sm"
                   onclick="return confirm('Terima permintaan dari <?= htmlspecialchars(addslashes($row['nama_lengkap'])); ?>?')">
                    ✅ Terima
                </a>
                <a href="notifikasi.php?tolak=<?= $row['id_pertukaran']; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Tolak permintaan dari <?= htmlspecialchars(addslashes($row['nama_lengkap'])); ?>?')">
                    ❌ Tolak
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; endif; ?>

</div>
</body>
</html>
