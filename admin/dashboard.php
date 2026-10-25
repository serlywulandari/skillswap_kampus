<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

$total_pengguna      = db_count("SELECT id_pengguna FROM pengguna WHERE level='mahasiswa'");
$total_keahlian      = db_count("SELECT id_keahlian FROM keahlian");
$total_permintaan    = db_count("SELECT id_pertukaran FROM permintaan_pertukaran");
$total_jadwal        = db_count("SELECT id_jadwal FROM jadwal_belajar");
$permintaan_menunggu = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Menunggu'");
$permintaan_diterima = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Diterima'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.dashboard-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;margin-bottom:28px;}
.stat-card{background:white;border-radius:18px;padding:30px 28px;box-shadow:0 4px 20px rgba(0,32,96,.08);display:flex;align-items:center;gap:20px;border-left:5px solid var(--navy);transition:transform .25s,box-shadow .25s;}
.stat-card:hover{transform:translateY(-4px);box-shadow:0 8px 30px rgba(0,32,96,.14);}
.stat-card:nth-child(2){border-left-color:var(--gold);}
.stat-card:nth-child(3){border-left-color:#16a34a;}
.stat-card:nth-child(4){border-left-color:#7c3aed;}
.stat-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;background:#eef2ff;flex-shrink:0;}
.stat-card:nth-child(1) .stat-icon{background:#e8f0fe;}
.stat-card:nth-child(2) .stat-icon{background:#fff8e1;}
.stat-card:nth-child(3) .stat-icon{background:#e8f5e9;}
.stat-card:nth-child(4) .stat-icon{background:#f3e8ff;}
.stat-info h2{font-size:36px;color:var(--navy);font-weight:800;margin-bottom:2px;}
.stat-info p{color:#6b7280;font-size:14px;font-weight:500;margin:0;}
.welcome-card{background:linear-gradient(135deg,var(--navy) 0%,#1a3a8a 100%);border-radius:18px;padding:36px 40px;color:white;margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 8px 25px rgba(0,32,96,.2);}
.welcome-card h2{font-size:24px;margin-bottom:8px;}
.welcome-card p{opacity:.85;font-size:15px;margin:0;}
.welcome-badge{background:var(--gold);color:var(--navy);padding:8px 20px;border-radius:30px;font-weight:700;font-size:14px;white-space:nowrap;}
.mini-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
.mini-stat{background:white;border-radius:12px;padding:18px 20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center;}
.mini-stat .label{color:#6b7280;font-size:13px;}
.mini-stat .value{font-size:22px;font-weight:700;color:var(--navy);}
.section-label{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;margin-bottom:14px;}
@media(max-width:768px){.dashboard-grid-2,.mini-stats{grid-template-columns:1fr;}.welcome-card{flex-direction:column;gap:12px;}}
</style>
</head>
<body>
<?php include "sidebar_admin.php"; ?>
<div class="main-content">
    <div class="topbar" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 style="font-size:22px;color:var(--navy);margin:0;">Dashboard Admin</h1>
            <p style="color:#6b7280;font-size:13px;margin:4px 0 0;">Selamat datang kembali!</p>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="text-align:right;">
                <div style="font-weight:700;color:var(--navy);"><?= htmlspecialchars($_SESSION['nama']); ?></div>
                <div style="font-size:12px;color:#9ca3af;">Administrator</div>
            </div>
            <div style="width:42px;height:42px;background:var(--navy);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:18px;">
                <?= strtoupper(substr($_SESSION['nama'],0,1)); ?>
            </div>
        </div>
    </div>
    <div class="welcome-card">
        <div>
            <h2>Halo, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋</h2>
            <p>Kelola pengguna, keahlian, jadwal belajar, dan aktivitas pertukaran mahasiswa dari sini.</p>
        </div>
        <div class="welcome-badge">SkillSwap Campus</div>
    </div>
    <div class="section-label">Ringkasan Data</div>
    <div class="dashboard-grid-2">
        <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-info"><h2><?= $total_pengguna; ?></h2><p>Total Mahasiswa</p></div></div>
        <div class="stat-card"><div class="stat-icon">🎯</div><div class="stat-info"><h2><?= $total_keahlian; ?></h2><p>Total Keahlian</p></div></div>
        <div class="stat-card"><div class="stat-icon">🔄</div><div class="stat-info"><h2><?= $total_permintaan; ?></h2><p>Total Pertukaran</p></div></div>
        <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-info"><h2><?= $total_jadwal; ?></h2><p>Total Jadwal</p></div></div>
    </div>
    <div class="section-label">Status Permintaan Pertukaran</div>
    <div class="mini-stats" style="margin-bottom:28px;">
        <div class="mini-stat"><div><div class="label">Menunggu</div><div class="value" style="color:#f59e0b;"><?= $permintaan_menunggu; ?></div></div><span style="font-size:28px;">⏳</span></div>
        <div class="mini-stat"><div><div class="label">Diterima</div><div class="value" style="color:#16a34a;"><?= $permintaan_diterima; ?></div></div><span style="font-size:28px;">✅</span></div>
    </div>
</div>
</body>
</html>
