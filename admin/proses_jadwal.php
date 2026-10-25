<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

// TAMBAH
if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $id_pertukaran = (int)($_POST['id_pertukaran'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $jam     = trim($_POST['jam'] ?? '');
    $lokasi  = trim($_POST['lokasi'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($id_pertukaran && $tanggal && $jam && $lokasi) {
        db_execute(
            "INSERT INTO jadwal_belajar (id_pertukaran, tanggal, jam, lokasi, catatan) VALUES (?, ?, ?, ?, ?)",
            "issss", $id_pertukaran, $tanggal, $jam, $lokasi, $catatan
        );
    }
    header("Location: jadwal.php?msg=added");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    db_execute("DELETE FROM jadwal_belajar WHERE id_jadwal = ?", "i", $id);
    header("Location: jadwal.php?msg=deleted");
    exit;
}

header("Location: jadwal.php");
exit;
