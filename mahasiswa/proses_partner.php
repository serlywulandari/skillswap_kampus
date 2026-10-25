<?php

session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$pengirim = (int)$_SESSION['id_pengguna'];
$penerima = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($penerima <= 0 || $penerima === $pengirim) {
    header("Location: cari_partner.php");
    exit;
}

// Pastikan penerima ada
$cek = db_query(
    "SELECT id_pengguna FROM pengguna WHERE id_pengguna = ? AND level = 'mahasiswa'",
    "i", $penerima
);
if (mysqli_num_rows($cek) === 0) {
    header("Location: cari_partner.php");
    exit;
}

// Cegah duplikat permintaan yang masih Menunggu
$duplikat = db_query(
    "SELECT id_pertukaran FROM permintaan_pertukaran
     WHERE pengirim = ? AND penerima = ? AND status = 'Menunggu'",
    "ii", $pengirim, $penerima
);
if (mysqli_num_rows($duplikat) > 0) {
    echo "<script>alert('Anda sudah memiliki permintaan yang sedang menunggu ke mahasiswa ini.'); window.location='cari_partner.php';</script>";
    exit;
}

db_execute(
    "INSERT INTO permintaan_pertukaran (pengirim, penerima, pesan, status)
     VALUES (?, ?, 'Saya ingin bertukar keahlian dengan Anda', 'Menunggu')",
    "ii", $pengirim, $penerima
);

header("Location: cari_partner.php");
exit;
?>
