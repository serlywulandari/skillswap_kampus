<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$msg = $_GET['msg'] ?? '';
$filter = $_GET['filter'] ?? '';

$where = '';
if ($filter === 'Menunggu' || $filter === 'Diterima' || $filter === 'Ditolak') {
    $where = "WHERE p.status = '" . htmlspecialchars($filter, ENT_QUOTES) . "'";
}

$data = db_raw(
    "SELECT p.id_pertukaran, u1.nama_lengkap AS pengirim, u1.program_studi AS prodi_pengirim,
            u2.nama_lengkap AS penerima, u2.program_studi AS prodi_penerima,
            p.pesan, p.status, p.tanggal_pengajuan
     FROM permintaan_pertukaran p
     JOIN pengguna u1 ON p.pengirim = u1.id_pengguna
     JOIN pengguna u2 ON p.penerima = u2.id_pengguna
     $where
     ORDER BY p.id_pertukaran DESC"
);
$rows = [];
while ($r = mysqli_fetch_assoc($data)) $rows[] = $r;

$total_menunggu = mysqli_num_rows(db_raw( "SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Menunggu'"));
$total_diterima = mysqli_num_rows(db_raw( "SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Diterima'"));
$total_ditolak  = mysqli_num_rows(db_raw( "SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Ditolak'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pertukaran Skill | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.filter-tabs { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
.filter-tab {
    padding: 7px 18px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    border: 1.5px solid #e5e7eb;
    color: #6b7280;
    background: white;
    transition: all .2s;
}
.filter-tab:hover { border-color: var(--navy); color: var(--navy); }
.filter-tab.active { background: var(--navy); color: white; border-color: var(--navy); }
.filter-tab.menunggu.active { background: #f59e0b; border-color: #f59e0b; }
.filter-tab.diterima.active { background: #16a34a; border-color: #16a34a; }
.filter-tab.ditolak.active  { background: #dc2626; border-color: #dc2626; }

.req-card {
    background: white;
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    border-left: 4px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 16px;
}
.req-card.menunggu { border-left-color: #f59e0b; }
.req-card.diterima { border-left-color: #16a34a; }
.req-card.ditolak  { border-left-color: #dc2626; }
.req-users { flex: 1; }
.req-names { font-weight: 700; color: var(--navy); font-size: 15px; }
.req-prodi { font-size: 12px; color: #9ca3af; margin-top: 2px; }
.req-date  { font-size: 12px; color: #9ca3af; margin-top: 6px; }
.req-actions { display:flex; gap:8px; flex-shrink:0; align-items:center; }
.btn-xs { padding: 6px 14px; font-size: 12px; border-radius: 8px; }
.badge-menunggu { background:#fff8e1; color:#b45309; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; }
.badge-diterima { background:#e8f5e9; color:#16a34a; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; }
.badge-ditolak  { background:#fee2e2; color:#dc2626; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; }
.summary-bar { display:flex; gap:12px; margin-bottom:22px; }
.sum-box { background:white; border-radius:12px; padding:14px 18px; box-shadow:0 2px 8px rgba(0,0,0,.06); flex:1; text-align:center; }
.sum-box .num { font-size:26px; font-weight:800; }
.sum-box .lbl { font-size:12px; color:#6b7280; margin-top:2px; }
.empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">🤝 Pertukaran Skill</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Monitor semua permintaan pertukaran antar mahasiswa</p>
        </div>
    </div>

    <?php if ($msg === 'hapus'): ?>
    <div class="alert-success-bar">🗑️ Permintaan berhasil dihapus.</div>
    <?php endif; ?>

    <!-- Summary -->
    <div class="summary-bar">
        <div class="sum-box">
            <div class="num" style="color:#f59e0b;"><?= $total_menunggu; ?></div>
            <div class="lbl">⏳ Menunggu</div>
        </div>
        <div class="sum-box">
            <div class="num" style="color:#16a34a;"><?= $total_diterima; ?></div>
            <div class="lbl">✅ Diterima</div>
        </div>
        <div class="sum-box">
            <div class="num" style="color:#dc2626;"><?= $total_ditolak; ?></div>
            <div class="lbl">❌ Ditolak</div>
        </div>
        <div class="sum-box">
            <div class="num" style="color:var(--navy);"><?= $total_menunggu + $total_diterima + $total_ditolak; ?></div>
            <div class="lbl">📊 Total</div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="permintaan.php" class="filter-tab <?= $filter===''?'active':''; ?>">Semua</a>
        <a href="permintaan.php?filter=Menunggu" class="filter-tab menunggu <?= $filter==='Menunggu'?'active':''; ?>">⏳ Menunggu (<?= $total_menunggu; ?>)</a>
        <a href="permintaan.php?filter=Diterima" class="filter-tab diterima <?= $filter==='Diterima'?'active':''; ?>">✅ Diterima (<?= $total_diterima; ?>)</a>
        <a href="permintaan.php?filter=Ditolak"  class="filter-tab ditolak  <?= $filter==='Ditolak'?'active':''; ?>">❌ Ditolak (<?= $total_ditolak; ?>)</a>
    </div>

    <?php if (empty($rows)): ?>
    <div class="card">
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:12px;">📭</div>
            <p style="font-size:16px; font-weight:600; color:var(--navy);">Tidak ada data</p>
            <p>Belum ada permintaan pertukaran<?= $filter ? " dengan status <strong>$filter</strong>" : ''; ?>.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
    <?php foreach ($rows as $row): ?>
    <div class="req-card <?= strtolower($row['status']); ?>">
        <div style="font-size:28px;">🔄</div>
        <div class="req-users">
            <div class="req-names">
                <?= htmlspecialchars($row['pengirim']); ?>
                <span style="color:#9ca3af; font-weight:400;"> ↔ </span>
                <?= htmlspecialchars($row['penerima']); ?>
            </div>
            <div class="req-prodi">
                <?= htmlspecialchars($row['prodi_pengirim'] ?: '-'); ?> &nbsp;·&nbsp; <?= htmlspecialchars($row['prodi_penerima'] ?: '-'); ?>
            </div>
            <div class="req-date">
                💬 <?= htmlspecialchars($row['pesan']); ?>&nbsp;&nbsp;
                🕐 <?= date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?>
            </div>
        </div>
        <div class="req-actions">
            <span class="badge-<?= strtolower($row['status']); ?>"><?= $row['status']; ?></span>
            <a href="proses_permintaan.php?hapus=<?= $row['id_pertukaran']; ?>"
               class="btn btn-danger btn-xs"
               onclick="return confirm('Hapus permintaan ini?')">Hapus</a>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <p style="color:#9ca3af; font-size:13px; margin-top:12px;">
        💡 <em>Mahasiswa dapat menerima atau menolak permintaan sendiri melalui halaman Notifikasi mereka.</em>
    </p>
</div>
</body>
</html>
