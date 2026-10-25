<?php
session_start();
include "../config/koneksi.php";
require_login('admin');

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    db_execute("DELETE FROM permintaan_pertukaran WHERE id_pertukaran = ?", "i", $id);
    header("Location: permintaan.php?msg=hapus");
    exit;
}

// Admin masih bisa override terima/tolak jika diperlukan
if (isset($_GET['terima'])) {
    $id = (int)$_GET['terima'];
    db_execute("UPDATE permintaan_pertukaran SET status='Diterima' WHERE id_pertukaran=?", "i", $id);
}
if (isset($_GET['tolak'])) {
    $id = (int)$_GET['tolak'];
    db_execute("UPDATE permintaan_pertukaran SET status='Ditolak' WHERE id_pertukaran=?", "i", $id);
}

header("Location: permintaan.php");
exit;
