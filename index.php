<?php
session_start();
include 'config/koneksi.php';

// Ambil statistik untuk halaman beranda
$total_mahasiswa  = db_count("SELECT id_pengguna FROM pengguna WHERE level='mahasiswa'");
$total_keahlian   = db_count("SELECT id_keahlian FROM keahlian");
$total_pertukaran = db_count("SELECT id_pertukaran FROM permintaan_pertukaran WHERE status='Diterima'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillSwap Campus — Platform Pertukaran Keahlian Mahasiswa</title>
<link rel="icon" href="assets/img/logo_uin.png">
<link rel="stylesheet" href="assets/css/style.css">
<style>
/* ===== NAVBAR ===== */
.navbar { background: var(--navy); position: sticky; top: 0; z-index: 999; box-shadow: 0 3px 15px rgba(0,0,0,.15); }
.navbar-container { display:flex; justify-content:space-between; align-items:center; padding: 14px 0; }
.logo-area { display:flex; align-items:center; gap:12px; color:white; text-decoration:none; }
.logo-kampus { width:44px; height:44px; object-fit:contain; background:white; border-radius:10px; padding:4px; }
.logo-area h3 { font-size:18px; margin:0; }
.logo-area small { font-size:11px; opacity:.75; display:block; }
.nav-menu { display:flex; align-items:center; gap:10px; }
.btn-nav { color:rgba(255,255,255,.85); text-decoration:none; font-weight:600; font-size:14px; padding:8px 16px; border-radius:8px; transition:.2s; }
.btn-nav:hover { color:white; background:rgba(255,255,255,.1); }
.btn-nav-cta { background:var(--gold); color:var(--navy) !important; border-radius:8px; padding:8px 18px; font-weight:700; }
.btn-nav-cta:hover { background:#e6ac00; }

/* ===== HERO ===== */
.hero-section { background: linear-gradient(135deg, #f0f5ff 0%, #eef4ff 50%, #f8fbff 100%); padding: 90px 0 80px; }
.hero-container { display:flex; justify-content:space-between; align-items:center; gap:60px; }
.hero-content { flex:1; max-width:560px; }
.hero-badge { display:inline-block; background:var(--gold); color:var(--navy); padding:8px 20px; border-radius:30px; font-weight:700; font-size:13px; margin-bottom:22px; }
.hero-content h1 { font-size:52px; color:var(--navy); line-height:1.15; margin-bottom:20px; }
.hero-content h1 span { color:#1a56d9; }
.hero-content p { font-size:17px; color:#555; margin-bottom:32px; line-height:1.7; }
.hero-button { display:flex; gap:14px; flex-wrap:wrap; }
.hero-image { flex:1; text-align:center; }
.hero-image img { width:100%; max-width:460px; filter:drop-shadow(0 20px 40px rgba(0,32,96,.12)); }

/* ===== STATS STRIP ===== */
.stats-strip { background:var(--navy); padding:40px 0; }
.stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:0; }
.stat-item { text-align:center; color:white; padding:10px 20px; border-right:1px solid rgba(255,255,255,.15); }
.stat-item:last-child { border-right:none; }
.stat-item .num { font-size:40px; font-weight:800; color:var(--gold); line-height:1; }
.stat-item .lbl { font-size:13px; opacity:.8; margin-top:6px; }

/* ===== FITUR ===== */
.fitur-section { padding:80px 0; }
.section-title { text-align:center; margin-bottom:50px; }
.section-title h2 { font-size:36px; color:var(--navy); margin-bottom:8px; }
.section-title p { color:#666; font-size:16px; }
.fitur-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:24px; }
.fitur-card { background:white; padding:30px; border-radius:18px; box-shadow:0 4px 18px rgba(0,32,96,.07); border:1.5px solid #eef2f7; transition:.3s; }
.fitur-card:hover { transform:translateY(-5px); border-color:var(--gold); box-shadow:0 10px 28px rgba(0,32,96,.12); }
.fitur-icon { font-size:36px; margin-bottom:14px; }
.fitur-card h3 { color:var(--navy); font-size:17px; margin-bottom:10px; }
.fitur-card p { color:#666; font-size:14px; line-height:1.65; }

/* ===== CARA KERJA ===== */
.cara-section { background:linear-gradient(135deg,#f0f5ff,#eef4ff); padding:80px 0; }
.steps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:30px; margin-top:50px; }
.step-card { text-align:center; }
.step-num { width:52px; height:52px; background:var(--navy); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800; margin:0 auto 16px; }
.step-card h4 { color:var(--navy); font-size:16px; margin-bottom:8px; }
.step-card p { color:#666; font-size:13px; line-height:1.6; }
.step-arrow { display:flex; align-items:center; justify-content:center; font-size:24px; color:#d1d5db; padding-top:26px; }

/* ===== CTA ===== */
.cta-section { padding:80px 0; }
.cta-box { background:linear-gradient(135deg,var(--navy),#1a3a8a); border-radius:24px; padding:60px 50px; text-align:center; color:white; box-shadow:0 15px 40px rgba(0,32,96,.2); }
.cta-box h2 { font-size:32px; margin-bottom:14px; }
.cta-box p { opacity:.85; font-size:16px; margin-bottom:30px; }
.cta-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }

/* ===== FOOTER ===== */
.footer { background:var(--navy); color:white; text-align:center; padding:50px 20px; }
.footer h3 { font-size:20px; margin-bottom:8px; }
.footer p { opacity:.7; font-size:14px; margin:4px 0; }
.footer .divider { width:40px; height:2px; background:var(--gold); margin:18px auto; }

/* RESPONSIVE */
@media(max-width:900px){
    .hero-container { flex-direction:column; text-align:center; }
    .hero-content { max-width:100%; }
    .hero-content h1 { font-size:38px; }
    .hero-button { justify-content:center; }
    .hero-image { display:none; }
    .stats-row { grid-template-columns:1fr; }
    .stat-item { border-right:none; border-bottom:1px solid rgba(255,255,255,.1); padding:16px; }
    .step-arrow { display:none; }
}
@media(max-width:600px){
    .hero-content h1 { font-size:30px; }
    .navbar-container { flex-direction:column; gap:12px; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container navbar-container">
        <a href="index.php" class="logo-area">
            <img src="assets/img/logo_uin.png" alt="Logo UIN" class="logo-kampus">
            <div>
                <h3>SkillSwap Campus</h3>
                <small>UIN Sultan Thaha Saifuddin Jambi</small>
            </div>
        </a>
        <div class="nav-menu">
            <?php if(isset($_SESSION['id_pengguna'])): ?>
                <?php if($_SESSION['level']==='admin'): ?>
                <a href="admin/dashboard.php" class="btn-nav">Dashboard Admin</a>
                <?php else: ?>
                <a href="mahasiswa/dashboard.php" class="btn-nav">Dashboard</a>
                <?php endif; ?>
                <a href="auth/logout.php" class="btn-nav">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn-nav">Login</a>
                <a href="auth/register.php" class="btn-nav btn-nav-cta">Daftar Sekarang</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section">
    <div class="container hero-container">
        <div class="hero-content">
            <span class="hero-badge">🎓 Platform Kolaborasi Mahasiswa</span>
            <h1>Belajar Bersama,<br><span>Bertumbuh Bersama</span></h1>
            <p>SkillSwap Campus membantu mahasiswa UIN Sultan Thaha Saifuddin Jambi menemukan partner belajar, bertukar keahlian, dan membangun jaringan akademik lintas prodi.</p>
            <?php if(!isset($_SESSION['id_pengguna'])): ?>
            <div class="hero-button">
                <a href="auth/register.php" class="btn btn-primary" style="font-size:15px; padding:14px 30px;">🚀 Mulai Sekarang</a>
                <a href="auth/login.php" class="btn btn-secondary" style="font-size:15px; padding:14px 30px;">Login</a>
            </div>
            <?php else: ?>
            <div class="hero-button">
                <a href="<?= $_SESSION['level']==='admin' ? 'admin/dashboard.php' : 'mahasiswa/dashboard.php'; ?>" class="btn btn-primary" style="font-size:15px; padding:14px 30px;">
                    Ke Dashboard →
                </a>
            </div>
            <?php endif; ?>
        </div>
        <div class="hero-image">
            <img src="assets/img/hero.png" alt="SkillSwap Campus">
        </div>
    </div>
</section>

<!-- STATS STRIP -->
<div class="stats-strip">
    <div class="container">
        <div class="stats-row">
            <div class="stat-item">
                <div class="num"><?= $total_mahasiswa; ?>+</div>
                <div class="lbl">Mahasiswa Terdaftar</div>
            </div>
            <div class="stat-item">
                <div class="num"><?= $total_keahlian; ?>+</div>
                <div class="lbl">Keahlian Tersedia</div>
            </div>
            <div class="stat-item">
                <div class="num"><?= $total_pertukaran; ?>+</div>
                <div class="lbl">Pertukaran Berhasil</div>
            </div>
        </div>
    </div>
</div>

<!-- FITUR -->
<section class="fitur-section">
    <div class="container">
        <div class="section-title">
            <h2>Fitur Unggulan</h2>
            <p>Semua yang Anda butuhkan untuk belajar kolaboratif</p>
        </div>
        <div class="fitur-grid">
            <div class="fitur-card">
                <div class="fitur-icon">🔍</div>
                <h3>Cari Partner Cerdas</h3>
                <p>Sistem matching score otomatis menemukan partner belajar yang paling sesuai dengan skill Anda.</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">🎯</div>
                <h3>Tukar Keahlian</h3>
                <p>Ajarkan apa yang Anda kuasai, pelajari apa yang Anda butuhkan. Saling menguntungkan!</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">📅</div>
                <h3>Jadwal Belajar</h3>
                <p>Atur sesi belajar bersama dengan mudah. Lihat jadwal mendatang dan riwayat sesi.</p>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">🔔</div>
                <h3>Notifikasi Real-time</h3>
                <p>Terima dan kelola permintaan pertukaran skill langsung dari panel notifikasi Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- CARA KERJA -->
<section class="cara-section">
    <div class="container">
        <div class="section-title">
            <h2>Cara Kerja</h2>
            <p>Mulai bertukar skill dalam 4 langkah mudah</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <h4>Daftar & Login</h4>
                <p>Buat akun mahasiswa dengan NIM dan email kampus Anda.</p>
            </div>
            <div class="step-arrow">→</div>
            <div class="step-card">
                <div class="step-num">2</div>
                <h4>Isi Profil Skill</h4>
                <p>Tambahkan skill yang Anda miliki dan skill yang ingin Anda pelajari.</p>
            </div>
            <div class="step-arrow">→</div>
            <div class="step-card">
                <div class="step-num">3</div>
                <h4>Cari Partner</h4>
                <p>Temukan mahasiswa dengan matching score tertinggi dan ajukan pertukaran.</p>
            </div>
            <div class="step-arrow">→</div>
            <div class="step-card">
                <div class="step-num">4</div>
                <h4>Belajar Bersama</h4>
                <p>Terima permintaan dan ikuti jadwal sesi belajar yang telah ditentukan.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<?php if(!isset($_SESSION['id_pengguna'])): ?>
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Siap Mulai Bertukar Skill? 🚀</h2>
            <p>Bergabunglah dengan mahasiswa UIN Jambi yang sudah aktif di SkillSwap Campus</p>
            <div class="cta-btns">
                <a href="auth/register.php" class="btn btn-secondary" style="font-size:15px; padding:14px 32px;">Daftar Gratis</a>
                <a href="auth/login.php" class="btn" style="background:rgba(255,255,255,.15); color:white; font-size:15px; padding:14px 32px;">Login</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <h3>SkillSwap Campus</h3>
        <div class="divider"></div>
        <p>UIN Sultan Thaha Saifuddin Jambi</p>
        <p>Sistem Informasi Pertukaran Keahlian Mahasiswa</p>
        <p style="margin-top:16px; opacity:.5;">© <?= date('Y'); ?> SkillSwap Campus. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
