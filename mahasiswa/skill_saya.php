<?php
session_start();
if(!isset($_SESSION["id_pengguna"]) || $_SESSION["level"] != "mahasiswa"){ header("Location: ../auth/login.php"); exit; }
include "../config/koneksi.php";

$id_pengguna = $_SESSION['id_pengguna'];
$msg = $_GET['msg'] ?? '';

$data = db_raw(
    "SELECT kd.id_keahlian_dimiliki, k.nama_keahlian
     FROM keahlian_dimiliki kd
     JOIN keahlian k ON kd.id_keahlian=k.id_keahlian
     WHERE kd.id_pengguna='$id_pengguna'
     ORDER BY k.nama_keahlian ASC"
);

$skill = db_raw( "SELECT * FROM keahlian ORDER BY nama_keahlian ASC");

$rows = [];
while ($r = mysqli_fetch_assoc($data)) $rows[] = $r;
$skills_list = [];
while ($s = mysqli_fetch_assoc($skill)) $skills_list[] = $s;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skill Saya | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.skill-chips { display:flex; flex-wrap:wrap; gap:12px; margin-top:6px; }
.skill-chip {
    background: #eef2ff;
    border: 1.5px solid #c7d2fe;
    border-radius: 30px;
    padding: 8px 18px;
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 600; color: var(--navy);
}
.skill-chip .chip-no { font-size:11px; color:#9ca3af; margin-right:2px; }
.chip-del {
    color: #dc2626;
    text-decoration: none;
    font-size: 16px;
    line-height: 1;
    font-weight: 700;
    transition: transform .15s;
}
.chip-del:hover { transform: scale(1.2); }
.empty-state { text-align:center; padding:40px; color:#9ca3af; }
.empty-state .es-icon { font-size:48px; margin-bottom:12px; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
.tambah-btn-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.count-chip { background:var(--gold); color:var(--navy); padding:4px 14px; border-radius:20px; font-size:13px; font-weight:700; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">💼 Skill Saya</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Daftar keahlian yang Anda miliki</p>
        </div>
    </div>

    <?php if ($msg === 'added'): ?>
    <div class="alert-success-bar">✅ Skill berhasil ditambahkan.</div>
    <?php endif; ?>

    <div class="card">
        <div class="tambah-btn-bar">
            <span class="count-chip"><?= count($rows); ?> Skill</span>
            <button class="btn btn-primary" style="padding:10px 20px; font-size:14px;"
                    onclick="document.getElementById('modalTambah').style.display='flex'">
                + Tambah Skill
            </button>
        </div>

        <?php if (empty($rows)): ?>
        <div class="empty-state">
            <div class="es-icon">💼</div>
            <p style="font-size:16px; font-weight:600; color:var(--navy);">Belum ada skill terdaftar</p>
            <p style="font-size:14px;">Tambahkan skill yang Anda kuasai agar partner lebih mudah menemukanmu.</p>
        </div>
        <?php else: ?>
        <div class="skill-chips">
        <?php foreach ($rows as $i => $row): ?>
            <div class="skill-chip">
                <span class="chip-no">#<?= $i+1; ?></span>
                🎯 <?= htmlspecialchars($row['nama_keahlian']); ?>
                <a class="chip-del"
                   href="proses_skill.php?hapus=<?= $row['id_keahlian_dimiliki']; ?>"
                   title="Hapus skill ini"
                   onclick="return confirm('Hapus skill <?= htmlspecialchars(addslashes($row['nama_keahlian'])); ?>?')">✕</a>
            </div>
        <?php endforeach; ?>
        </div>

        <div style="margin-top:24px; padding-top:20px; border-top:1px solid #f0f0f0;">
            <table>
            <tr><th>No</th><th>Nama Skill</th><th>Aksi</th></tr>
            <?php foreach ($rows as $i => $row): ?>
            <tr>
                <td style="text-align:center; color:#9ca3af;"><?= $i+1; ?></td>
                <td style="font-weight:600; color:var(--navy);"><?= htmlspecialchars($row['nama_keahlian']); ?></td>
                <td>
                    <a href="proses_skill.php?hapus=<?= $row['id_keahlian_dimiliki']; ?>"
                       class="btn btn-danger" style="padding:6px 14px; font-size:13px;"
                       onclick="return confirm('Hapus skill ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Skill -->
<div id="modalTambah" class="modal" style="display:none;">
<div class="modal-content">
    <button class="modal-close" onclick="document.getElementById('modalTambah').style.display='none'">✕</button>
    <h2>➕ Tambah Skill Saya</h2>
    <p style="color:#6b7280; font-size:14px; margin:-10px 0 20px;">Pilih skill dari daftar yang tersedia.</p>

    <form method="POST" action="proses_skill.php">
    <input type="hidden" name="aksi" value="tambah">
    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Pilih Skill</label>
    <select name="id_keahlian" required style="margin-bottom:16px;">
        <option value="">-- Pilih Skill --</option>
        <?php foreach ($skills_list as $s): ?>
        <option value="<?= $s['id_keahlian']; ?>"><?= htmlspecialchars($s['nama_keahlian']); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary" style="width:100%;">💾 Simpan Skill</button>
    </form>
</div>
</div>

<script>
window.onclick = function(e) {
    const m = document.getElementById('modalTambah');
    if (e.target === m) m.style.display = 'none';
}
</script>
</body>
</html>
