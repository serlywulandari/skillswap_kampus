<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id_saya = (int)$_SESSION['id_pengguna'];

$res_skill_saya = db_query(
    "SELECT id_keahlian FROM keahlian_dimiliki WHERE id_pengguna = ?", "i", $id_saya);
$skill_saya = [];
while ($r = mysqli_fetch_assoc($res_skill_saya)) $skill_saya[] = (int)$r['id_keahlian'];

$res_ingin_belajar = db_query(
    "SELECT id_keahlian FROM keahlian_dipelajari WHERE id_pengguna = ?", "i", $id_saya);
$ingin_belajar = [];
while ($r = mysqli_fetch_assoc($res_ingin_belajar)) $ingin_belajar[] = (int)$r['id_keahlian'];

$data_user = db_query(
    "SELECT id_pengguna, nama_lengkap, program_studi, fakultas
     FROM pengguna
     WHERE id_pengguna != ? AND level = 'mahasiswa'
     ORDER BY nama_lengkap ASC",
    "i", $id_saya
);

$partners = [];
while ($u = mysqli_fetch_assoc($data_user)) {
    $id_p = (int)$u['id_pengguna'];

    $res_p_skill = db_query(
        "SELECT k.id_keahlian, k.nama_keahlian FROM keahlian_dimiliki kd
         JOIN keahlian k ON kd.id_keahlian=k.id_keahlian WHERE kd.id_pengguna = ?", "i", $id_p);
    $skill_partner = []; $skill_partner_names = [];
    while ($r = mysqli_fetch_assoc($res_p_skill)) {
        $skill_partner[] = (int)$r['id_keahlian'];
        $skill_partner_names[] = $r['nama_keahlian'];
    }

    $res_p_belajar = db_query(
        "SELECT id_keahlian FROM keahlian_dipelajari WHERE id_pengguna = ?", "i", $id_p);
    $belajar_partner = [];
    while ($r = mysqli_fetch_assoc($res_p_belajar)) $belajar_partner[] = (int)$r['id_keahlian'];

    $saya_bisa_ajar   = count(array_intersect($skill_saya, $belajar_partner));
    $partner_bisa_ajar= count(array_intersect($skill_partner, $ingin_belajar));
    $total_poin = $saya_bisa_ajar + $partner_bisa_ajar;
    $max_poin = max(1, count($ingin_belajar) + count($belajar_partner));
    $score = min(100, (int)round(($total_poin / $max_poin) * 100));
    if ($score < 10) $score = 10;

    $u['matching'] = $score;
    $u['skill_partner_names'] = array_slice($skill_partner_names, 0, 3);
    $partners[] = $u;
}
usort($partners, fn($a, $b) => $b['matching'] - $a['matching']);

$no_skill = empty($skill_saya) && empty($ingin_belajar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cari Partner | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.partner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
    margin-top: 8px;
}
.partner-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,32,96,.07);
    border: 1.5px solid #f0f0f0;
    transition: border .2s, box-shadow .2s;
}
.partner-card:hover { border-color: var(--navy); box-shadow: 0 6px 20px rgba(0,32,96,.12); }
.partner-top { display:flex; align-items:center; gap:14px; margin-bottom:14px; }
.partner-avatar {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, var(--navy), #1a3a8a);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: 20px; flex-shrink:0;
}
.partner-name { font-weight: 700; color: var(--navy); font-size: 15px; }
.partner-prodi { font-size: 12px; color: #9ca3af; }
.match-bar-wrap { margin: 12px 0; }
.match-label { display:flex; justify-content:space-between; font-size:12px; color:#6b7280; margin-bottom:5px; }
.match-bar { height: 7px; background: #f0f0f0; border-radius: 10px; overflow: hidden; }
.match-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #16a34a, #4ade80); transition: width .6s; }
.match-fill.mid { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
.match-fill.low { background: linear-gradient(90deg, #dc2626, #f87171); }
.skill-tags { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px; }
.skill-tag { background:#eef2ff; color:var(--navy); padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
.no-skill-warning {
    background: #fff8e1;
    border: 1.5px solid var(--gold);
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 20px;
    display:flex; align-items:flex-start; gap:14px;
}
.no-skill-warning .ws-text { font-size:14px; color:#92400e; }
.no-skill-warning a { color:var(--navy); font-weight:600; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">🔍 Cari Partner Belajar</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Temukan mahasiswa yang cocok untuk bertukar skill</p>
        </div>
        <span style="background:var(--gold); color:var(--navy); padding:5px 16px; border-radius:20px; font-size:13px; font-weight:700;">
            <?= count($partners); ?> Mahasiswa
        </span>
    </div>

    <?php if ($no_skill): ?>
    <div class="no-skill-warning">
        <span style="font-size:22px;">⚠️</span>
        <div class="ws-text">
            <strong>Profil skill Anda belum lengkap.</strong><br>
            Tambahkan <a href="skill_saya.php">Skill Saya</a> dan <a href="skill_dipelajari.php">Skill Dipelajari</a>
            agar sistem dapat menghitung matching score yang akurat dan menemukan partner terbaik untuk Anda.
        </div>
    </div>
    <?php endif; ?>

    <div class="partner-grid">
    <?php foreach ($partners as $u): ?>
    <div class="partner-card">
        <div class="partner-top">
            <div class="partner-avatar"><?= strtoupper(substr($u['nama_lengkap'], 0, 1)); ?></div>
            <div>
                <div class="partner-name"><?= htmlspecialchars($u['nama_lengkap']); ?></div>
                <div class="partner-prodi"><?= htmlspecialchars($u['program_studi'] ?: 'Mahasiswa'); ?></div>
            </div>
        </div>

        <?php if (!empty($u['skill_partner_names'])): ?>
        <div class="skill-tags">
            <?php foreach ($u['skill_partner_names'] as $sn): ?>
            <span class="skill-tag">🎯 <?= htmlspecialchars($sn); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="match-bar-wrap">
            <div class="match-label">
                <span>Matching Score</span>
                <span style="font-weight:700; color:<?= $u['matching'] >= 60 ? '#16a34a' : ($u['matching'] >= 30 ? '#f59e0b' : '#dc2626'); ?>">
                    <?= $u['matching']; ?>%
                </span>
            </div>
            <div class="match-bar">
                <div class="match-fill <?= $u['matching'] < 30 ? 'low' : ($u['matching'] < 60 ? 'mid' : ''); ?>"
                     style="width:<?= $u['matching']; ?>%"></div>
            </div>
        </div>

        <a href="proses_partner.php?id=<?= $u['id_pengguna']; ?>"
           class="btn btn-primary" style="width:100%; text-align:center; padding:10px;"
           onclick="return confirm('Ajukan permintaan pertukaran ke <?= htmlspecialchars(addslashes($u['nama_lengkap'])); ?>?')">
            🤝 Ajukan Pertukaran
        </a>
    </div>
    <?php endforeach; ?>
    </div>

</div>
</body>
</html>
