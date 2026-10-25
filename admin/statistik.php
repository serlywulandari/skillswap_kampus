<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$total_pengguna      = db_count("SELECT id_pengguna FROM pengguna WHERE level='mahasiswa'");
$total_keahlian      = db_count("SELECT id_keahlian FROM keahlian");
$total_permintaan    = db_count("SELECT id_pertukaran FROM permintaan_pertukaran");
$total_jadwal        = db_count("SELECT id_jadwal FROM jadwal_belajar");
$menunggu            = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Menunggu'");
$diterima            = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Diterima'");
$ditolak             = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Ditolak'");
$total_skill_dimiliki  = db_count("SELECT id_keahlian_dimiliki FROM keahlian_dimiliki");
$total_skill_dipelajari= db_count("SELECT id_keahlian_dipelajari FROM keahlian_dipelajari");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statistik | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.stat-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:24px;}
.stat-box{background:white;border-radius:16px;padding:26px 24px;box-shadow:0 4px 16px rgba(0,32,96,.07);display:flex;align-items:center;gap:16px;border-left:4px solid var(--navy);transition:transform .2s;}
.stat-box:hover{transform:translateY(-3px);}
.stat-box:nth-child(2){border-left-color:#f59e0b;}
.stat-box:nth-child(3){border-left-color:#16a34a;}
.stat-box:nth-child(4){border-left-color:#7c3aed;}
.stat-box .icon{font-size:32px;width:52px;text-align:center;}
.stat-box .num{font-size:32px;font-weight:800;color:var(--navy);line-height:1;}
.stat-box .lbl{color:#6b7280;font-size:13px;margin-top:3px;}
.section-title-sm{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;margin:24px 0 14px;}
.status-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;}
.status-pill{background:white;border-radius:14px;padding:20px 22px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center;}
.status-pill .sp-label{color:#6b7280;font-size:13px;font-weight:500;}
.status-pill .sp-value{font-size:28px;font-weight:800;}
.skill-row{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
.skill-box{background:white;border-radius:14px;padding:22px 24px;box-shadow:0 2px 10px rgba(0,0,0,.06);}
.skill-box h3{color:var(--navy);font-size:15px;margin-bottom:6px;}
.skill-box .big{font-size:36px;font-weight:800;color:var(--navy);}
.skill-box p{color:#6b7280;font-size:13px;margin:0;}
@media(max-width:768px){.stat-grid-2,.status-grid,.skill-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">
    <div class="topbar" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 style="font-size:22px;color:var(--navy);margin:0;">Statistik Sistem</h1>
            <p style="color:#6b7280;font-size:13px;margin:4px 0 0;">Ringkasan keseluruhan data platform</p>
        </div>
        <div style="font-weight:600;color:#6b7280;font-size:14px;">📊 Data Terkini</div>
    </div>
    <div class="section-title-sm">Data Utama</div>
    <div class="stat-grid-2">
        <div class="stat-box"><div class="icon">👥</div><div><div class="num"><?= $total_pengguna; ?></div><div class="lbl">Mahasiswa Terdaftar</div></div></div>
        <div class="stat-box"><div class="icon">🎯</div><div><div class="num"><?= $total_keahlian; ?></div><div class="lbl">Keahlian Tersedia</div></div></div>
        <div class="stat-box"><div class="icon">🔄</div><div><div class="num"><?= $total_permintaan; ?></div><div class="lbl">Total Pertukaran</div></div></div>
        <div class="stat-box"><div class="icon">📅</div><div><div class="num"><?= $total_jadwal; ?></div><div class="lbl">Jadwal Belajar</div></div></div>
    </div>
    <div class="section-title-sm">Status Pertukaran</div>
    <div class="status-grid">
        <div class="status-pill"><div><div class="sp-label">⏳ Menunggu</div><div class="sp-value" style="color:#f59e0b;"><?= $menunggu; ?></div></div></div>
        <div class="status-pill"><div><div class="sp-label">✅ Diterima</div><div class="sp-value" style="color:#16a34a;"><?= $diterima; ?></div></div></div>
        <div class="status-pill"><div><div class="sp-label">❌ Ditolak</div><div class="sp-value" style="color:#dc2626;"><?= $ditolak; ?></div></div></div>
        <div class="status-pill"><div><div class="sp-label">📊 Tingkat Diterima</div><div class="sp-value" style="color:var(--navy);font-size:22px;"><?= $total_permintaan > 0 ? round(($diterima/$total_permintaan)*100) : 0; ?>%</div></div></div>
    </div>
    <div class="section-title-sm">Aktivitas Skill</div>
    <div class="skill-row">
        <div class="skill-box"><h3>💼 Skill Dimiliki</h3><div class="big"><?= $total_skill_dimiliki; ?></div><p>Total skill yang sudah didaftarkan mahasiswa</p></div>
        <div class="skill-box"><h3>📚 Skill Dipelajari</h3><div class="big"><?= $total_skill_dipelajari; ?></div><p>Total skill yang ingin dipelajari mahasiswa</p></div>
    </div>
</div>
</body>
</html>
