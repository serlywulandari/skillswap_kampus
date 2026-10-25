<?php
session_start();
if(!isset($_SESSION['id_pengguna'])){ header("Location: ../auth/login.php"); exit; }
if($_SESSION['level'] != 'mahasiswa'){ header("Location: ../index.php"); exit; }
include "../config/koneksi.php";

$id_pengguna = $_SESSION['id_pengguna'];
$msg = $_GET['msg'] ?? '';

$data = db_raw(
    "SELECT kd.id_keahlian_dipelajari, k.nama_keahlian
     FROM keahlian_dipelajari kd
     JOIN keahlian k ON kd.id_keahlian = k.id_keahlian
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
<title>Skill Dipelajari | SkillSwap Campus</title>
<link rel="icon" type="image/png" href="../assets/img/logo_uin.png">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.skill-chips { display:flex; flex-wrap:wrap; gap:12px; margin-top:6px; }
.skill-chip {
    background: #fff8e1;
    border: 1.5px solid #fde68a;
    border-radius: 30px;
    padding: 8px 18px;
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 600; color: #92400e;
}
.skill-chip .chip-no { font-size:11px; color:#b45309; margin-right:2px; }
.chip-del { color:#dc2626; text-decoration:none; font-size:16px; font-weight:700; transition:transform .15s; }
.chip-del:hover { transform:scale(1.2); }
.empty-state { text-align:center; padding:40px; color:#9ca3af; }
.empty-state .es-icon { font-size:48px; margin-bottom:12px; }
.alert-success-bar { background:#e8f5e9; color:#16a34a; border:1px solid #c3e6cb; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; }
.count-chip { background:var(--gold); color:var(--navy); padding:4px 14px; border-radius:20px; font-size:13px; font-weight:700; }
</style>
</head>
<body>
<?php include "sidebar_mahasiswa.php"; ?>
<div class="main-content">

    <div class="topbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="font-size:22px; color:var(--navy); margin:0;">📚 Skill Yang Ingin Dipelajari</h1>
            <p style="color:#6b7280; font-size:13px; margin:4px 0 0;">Skill yang ingin Anda pelajari dari orang lain</p>
        </div>
    </div>

    <?php if ($msg === 'added'): ?>
    <div class="alert-success-bar">✅ Skill berhasil ditambahkan ke daftar belajar.</div>
    <?php endif; ?>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <span class="count-chip"><?= count($rows); ?> Skill</span>
            <button class="btn btn-primary" style="padding:10px 20px; font-size:14px;"
                    onclick="document.getElementById('modalTambah').style.display='flex'">
                + Tambah Skill
            </button>
        </div>

        <?php if (empty($rows)): ?>
        <div class="empty-state">
            <div class="es-icon">📚</div>
            <p style="font-size:16px; font-weight:600; color:var(--navy);">Belum ada skill dipelajari</p>
            <p style="font-size:14px;">Tambahkan skill yang ingin Anda pelajari agar sistem bisa mencarikan partner yang tepat.</p>
        </div>
        <?php else: ?>
        <div class="skill-chips">
        <?php foreach ($rows as $i => $row): ?>
            <div class="skill-chip">
                <span class="chip-no">#<?= $i+1; ?></span>
                📖 <?= htmlspecialchars($row['nama_keahlian']); ?>
                <a class="chip-del"
                   href="proses_skill_dipelajari.php?hapus=<?= $row['id_keahlian_dipelajari']; ?>"
                   title="Hapus dari daftar"
                   onclick="return confirm('Hapus skill <?= htmlspecialchars(addslashes($row['nama_keahlian'])); ?> dari daftar?')">✕</a>
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
                    <a href="proses_skill_dipelajari.php?hapus=<?= $row['id_keahlian_dipelajari']; ?>"
                       class="btn btn-danger" style="padding:6px 14px; font-size:13px;"
                       onclick="return confirm('Hapus dari daftar?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Skill Dipelajari -->
<div id="modalTambah" class="modal" style="display:none;">
<div class="modal-content">
    <button class="modal-close" onclick="document.getElementById('modalTambah').style.display='none'">✕</button>
    <h2>➕ Tambah Skill Dipelajari</h2>
    <p style="color:#6b7280; font-size:14px; margin:-10px 0 20px;">Pilih skill yang ingin Anda pelajari.</p>

    <form method="POST" action="proses_skill_dipelajari.php">
    <input type="hidden" name="aksi" value="tambah">
    <label style="font-size:13px; font-weight:600; color:var(--navy); display:block; margin-bottom:6px;">Pilih Skill</label>
    <select name="id_keahlian" required style="margin-bottom:16px;">
        <option value="">-- Pilih Skill --</option>
        <?php foreach ($skills_list as $s): ?>
        <option value="<?= $s['id_keahlian']; ?>"><?= htmlspecialchars($s['nama_keahlian']); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary" style="width:100%;">💾 Simpan</button>
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
