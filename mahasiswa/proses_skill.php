<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id_pengguna = (int)$_SESSION['id_pengguna'];

if (isset($_POST['aksi'])) {
    $id_keahlian = (int)($_POST['id_keahlian'] ?? 0);
    if ($id_keahlian > 0) {
        $cek = db_query(
            "SELECT id_keahlian_dimiliki FROM keahlian_dimiliki WHERE id_pengguna = ? AND id_keahlian = ?",
            "ii", $id_pengguna, $id_keahlian);
        if (mysqli_num_rows($cek) == 0) {
            db_execute(
                "INSERT INTO keahlian_dimiliki (id_pengguna, id_keahlian) VALUES (?, ?)",
                "ii", $id_pengguna, $id_keahlian);
        }
    }
    header("Location: skill_saya.php?msg=added");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    db_execute(
        "DELETE FROM keahlian_dimiliki WHERE id_keahlian_dimiliki = ? AND id_pengguna = ?",
        "ii", $id, $id_pengguna);
    header("Location: skill_saya.php");
    exit;
}

header("Location: skill_saya.php");
