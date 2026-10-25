<?php
$halaman = basename($_SERVER['PHP_SELF']);

// Hitung permintaan menunggu
$_sid = (int)($_SESSION['id_pengguna'] ?? 0);
$_notif = 0;
if ($_sid) {
    $r = db_raw(
        "SELECT COUNT(*) as n FROM permintaan_pertukaran WHERE penerima='$_sid' AND status='Menunggu'");
    if ($r) { $rw = mysqli_fetch_assoc($r); $_notif = (int)$rw['n']; }
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/img/logo_uin.png" alt="Logo UIN" class="sidebar-logo">
        <h2>SkillSwap</h2>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Mahasiswa'); ?></strong>
            <span>Mahasiswa</span>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?= $halaman=='dashboard.php'?'active':''; ?>">🏠 Dashboard</a>
        <a href="cari_partner.php" class="<?= $halaman=='cari_partner.php'?'active':''; ?>">🔍 Cari Partner</a>
        <a href="skill_saya.php" class="<?= $halaman=='skill_saya.php'?'active':''; ?>">🎯 Skill Saya</a>
        <a href="skill_dipelajari.php" class="<?= $halaman=='skill_dipelajari.php'?'active':''; ?>">📚 Skill Dipelajari</a>
        <a href="jadwal.php" class="<?= $halaman=='jadwal.php'?'active':''; ?>">📅 Jadwal Belajar</a>
        <a href="notifikasi.php" class="<?= $halaman=='notifikasi.php'?'active':''; ?>" style="position:relative;">
            🔔 Notifikasi
            <?php if ($_notif > 0): ?>
            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                         background:#dc2626; color:white; font-size:11px; font-weight:700;
                         min-width:20px; height:20px; border-radius:10px;
                         display:flex; align-items:center; justify-content:center; padding:0 5px;">
                <?= $_notif; ?>
            </span>
            <?php endif; ?>
        </a>
        <a href="profil.php" class="<?= $halaman=='profil.php'?'active':''; ?>">👤 Profil Saya</a>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="logout-btn">🚪 Logout</a>
    </div>
</div>
