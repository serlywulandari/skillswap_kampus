<?php
// Tentukan halaman aktif
$halaman = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="sidebar-header">
        <img src="../assets/img/logo_uin.png" alt="Logo UIN" class="sidebar-logo">
        <h2>SkillSwap</h2>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?></strong>
            <span>Admin Sistem</span>
        </div>
    </div>

    <div class="sidebar-menu">

        <a href="dashboard.php"
           class="<?= $halaman == 'dashboard.php' ? 'active' : ''; ?>">
            🏠 Dashboard
        </a>

        <a href="pengguna.php"
           class="<?= $halaman == 'pengguna.php' ? 'active' : ''; ?>">
            👥 Data Pengguna
        </a>

        <a href="keahlian.php"
           class="<?= $halaman == 'keahlian.php' ? 'active' : ''; ?>">
            🎯 Data Keahlian
        </a>

        <a href="permintaan.php"
           class="<?= $halaman == 'permintaan.php' ? 'active' : ''; ?>">
            🤝 Pertukaran Skill
        </a>

        <a href="jadwal.php"
           class="<?= $halaman == 'jadwal.php' ? 'active' : ''; ?>">
            📅 Jadwal Belajar
        </a>

        <a href="statistik.php"
           class="<?= $halaman == 'statistik.php' ? 'active' : ''; ?>">
            📊 Statistik
        </a>

    </div>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="logout-btn">
            🚪 Logout
        </a>
    </div>

</div>
