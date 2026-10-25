<?php
session_start();
if(!isset($_SESSION['id_pengguna'])){ header("Location: ../auth/login.php"); exit; }
if($_SESSION['level'] != 'mahasiswa'){ header("Location: ../index.php"); exit; }
include "../config/koneksi.php";

$id_pengguna = (int)$_SESSION['id_pengguna'];

$total_skill = mysqli_num_rows(db_query(
    "SELECT id_keahlian_dimiliki FROM keahlian_dimiliki WHERE id_pengguna=?", "i", $id_pengguna));
$total_belajar = mysqli_num_rows(db_query(
    "SELECT id_keahlian_dipelajari FROM keahlian_dipelajari WHERE id_pengguna=?", "i", $id_pengguna));
$total_permintaan = mysqli_num_rows(db_query(
    "SELECT id_pertukaran FROM permintaan_pertukaran WHERE pengirim=? OR penerima=?", "ii", $id_pengguna, $id_pengguna));
$total_jadwal = mysqli_num_rows(db_query(
    "SELECT jb.id_jadwal FROM jadwal_belajar jb
     JOIN permintaan_pertukaran pp ON jb.id_pertukaran=pp.id_pertukaran
     WHERE pp.pengirim=? OR pp.penerima=?", "ii", $id_pengguna, $id_pengguna));
$permintaan_masuk = mysqli_num_rows(db_query(
    "SELECT id_pertukaran FROM permintaan_pertukaran WHERE penerima=? AND status='Menunggu'", "i", $id_pengguna));

$profil = mysqli_fetch_assoc(db_query(
    "SELECT * FROM pengguna WHERE id_pengguna=?", "i", $id_pengguna));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | SkillSwap Campus</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<style>
.dashboard-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}
.stat-card {
    background: white;
    border-radius: 18px;
    padding: 26px 24px;
    box-shadow: 0 4px 16px rgba(0,32,96,.07);
    display: flex;
    align-items: center;
    gap: 16px;
    border-left: 4px solid var(--navy);
    transition: transform .22s, box-shadow .22s;
    text-decoration: none;
    color: inherit;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,32,96,.13); }
.stat-card:nth-child(2) { border-left-color: #f59e0b; }
.stat-card:nth-child(3) { border-left-color: #16a34a; }
.stat-card:nth-child(4) { border-left-color: #7c3aed; }
.stat-icon { font-size: 28px; width: 50px; text-align: center; flex-shrink: 0; }
.stat-num { font-size: 32px; font-weight: 800; color: var(--navy); line-height: 1; }
.stat-lbl { color: #6b7280; font-size: 13px; margin-top: 3px; }
.welcome-card {
    background: linear-gradient(135deg, var(--navy) 0%, #1a3a8a 100%);
    border-radius: 18px;
    padding: 30px 36px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 25px rgba(0,32,96,.2);
}
.welcome-card h2 { font-size: 20px; margin: 0 0 6px; }
.welcome-card p { opacity: .8; font-size: 14px; margin: 0; }
.welcome-meta .prodi { font-size: 13px; font-weight: 600; color: var(--gold); text-align:right; }
.welcome-meta .nim   { font-size: 12px; opacity: .65; text-align:right; }
.section-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:12px; }
.notif-banner {
    background: #fff8e1; border: 1.5px solid var(--gold);
    border-radius: 14px; padding: 16px 20px;
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 22px; text-decoration: none;
    transition: background .2s;
}
.notif-banner:hover { background: #fff3cd; }
.notif-banner span { color: var(--navy); font-weight: 600; font-size: 14px; }
.quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.quick-link {
    background: white; border-radius: 14px; padding: 18px 20px;
    text-decoration: none; display: flex; align-items: center; gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06); border: 1.5px solid #f0f0f0;
    transition: border .2s, box-shadow .2s;
}
.quick-link:hover { border-color: var(--navy); box-shadow: 0 4px 16px rgba(0,32,96,.1); }
.quick-link .ql-icon { font-size: 22px; }
.quick-link .ql-text { font-weight: 600; color: var(--navy); font-size: 14px; }
.quick-link .ql-sub  { font-size: 12px; color: #9ca3af; }
@media(max-width:768px){ .dashboard-grid-2, .quick-links { grid-template-columns:1fr; } .welcome-card { flex-direction:column; gap:12px; } }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">Dashboard</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">SkillSwap Campus</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="text-align:right;">
                <div style="font-weight:700; color:var(--navy); font-size:14px;"><?= htmlspecialchars($_SESSION['nama']); ?></div>
                <div style="font-size:12px; color:#9ca3af;">Mahasiswa</div>
            </div>
            <div style="width:40px; height:40px; background:var(--navy); border-radius:12px;
                        display:flex; align-items:center; justify-content:center;
                        color:white; font-weight:700; font-size:16px;">
                <?= strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-card">
        <div>
            <h2>Halo, <?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]); ?>! 👋</h2>
            <p>Temukan partner belajar dan tingkatkan keahlianmu bersama.</p>
        </div>
        <div class="welcome-meta">
            <div class="prodi"><?= htmlspecialchars($profil['program_studi'] ?: 'SkillSwap Campus'); ?></div>
            <div class="nim"><?= htmlspecialchars($profil['nim']); ?></div>
        </div>
    </div>

    <!-- Notif Banner -->
    <?php if ($permintaan_masuk > 0): ?>
    <a href="notifikasi.php" class="notif-banner">
        <span style="font-size:22px;">🔔</span>
        <span>Ada <strong><?= $permintaan_masuk; ?> permintaan pertukaran</strong> yang menunggu responmu!</span>
        <span style="margin-left:auto; color:var(--navy);">→</span>
    </a>
    <?php endif; ?>

    <!-- Stats 2 kolom -->
    <div class="section-label">Ringkasan Aktivitas</div>
    <div class="dashboard-grid-2">
        <a href="skill_saya.php" class="stat-card">
            <div class="stat-icon">💼</div>
            <div><div class="stat-num"><?= $total_skill; ?></div><div class="stat-lbl">Skill Saya</div></div>
        </a>
        <a href="skill_dipelajari.php" class="stat-card">
            <div class="stat-icon">📚</div>
            <div><div class="stat-num"><?= $total_belajar; ?></div><div class="stat-lbl">Skill Dipelajari</div></div>
        </a>
        <a href="notifikasi.php" class="stat-card">
            <div class="stat-icon">🔄</div>
            <div><div class="stat-num"><?= $total_permintaan; ?></div><div class="stat-lbl">Pertukaran</div></div>
        </a>
        <a href="jadwal.php" class="stat-card">
            <div class="stat-icon">📅</div>
            <div><div class="stat-num"><?= $total_jadwal; ?></div><div class="stat-lbl">Jadwal Belajar</div></div>
        </a>
    </div>

    <!-- Quick Links -->
    <div class="section-label">Menu Cepat</div>
    <div class="quick-links">
        <a href="cari_partner.php" class="quick-link">
            <span class="ql-icon">🔍</span>
            <div><div class="ql-text">Cari Partner</div><div class="ql-sub">Temukan teman belajar</div></div>
        </a>
        <a href="skill_saya.php" class="quick-link">
            <span class="ql-icon">✏️</span>
            <div><div class="ql-text">Kelola Skill</div><div class="ql-sub">Tambah atau hapus skill</div></div>
        </a>
        <a href="jadwal.php" class="quick-link">
            <span class="ql-icon">📆</span>
            <div><div class="ql-text">Jadwal Belajar</div><div class="ql-sub">Lihat jadwal sesi</div></div>
        </a>
        <a href="profil.php" class="quick-link">
            <span class="ql-icon">👤</span>
            <div><div class="ql-text">Profil Saya</div><div class="ql-sub">Update data profil</div></div>
        </a>
    </div>

</div>
</body>
</html>
