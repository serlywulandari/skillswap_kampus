<?php

session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id_pengguna = (int)$_SESSION['id_pengguna'];

if (isset($_POST['simpan'])) {
    $id_pertukaran = (int)($_POST['id_pertukaran'] ?? 0);
    $tanggal       = $_POST['tanggal'] ?? '';
    $jam           = $_POST['jam'] ?? '';
    $lokasi        = trim($_POST['lokasi'] ?? '');
    $catatan       = trim($_POST['catatan'] ?? '');

    // Pastikan pertukaran memang milik user ini
    $cek = db_query(
        "SELECT id_pertukaran FROM permintaan_pertukaran
         WHERE id_pertukaran = ? AND (pengirim = ? OR penerima = ?) AND status = 'Diterima'",
        "iii", $id_pertukaran, $id_pengguna, $id_pengguna
    );

    if (mysqli_num_rows($cek) > 0 && !empty($tanggal) && !empty($jam)) {
        db_execute(
            "INSERT INTO jadwal_belajar (id_pertukaran, tanggal, jam, lokasi, catatan)
             VALUES (?, ?, ?, ?, ?)",
            "issss", $id_pertukaran, $tanggal, $jam, $lokasi, $catatan
        );
    }
}

header("Location: jadwal.php");
exit;
?>
