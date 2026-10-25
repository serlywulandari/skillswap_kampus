<?php
session_start();
include "../config/koneksi.php";
require_login('mahasiswa');

$id = (int)$_SESSION['id_pengguna'];

$nama    = trim($_POST['nama_lengkap'] ?? '');
$email   = trim($_POST['email'] ?? '');
$fakultas= trim($_POST['fakultas'] ?? '');
$prodi   = trim($_POST['program_studi'] ?? '');

db_execute(
    "UPDATE pengguna SET nama_lengkap=?, email=?, fakultas=?, program_studi=? WHERE id_pengguna=?",
    "ssssi", $nama, $email, $fakultas, $prodi, $id
);

// Update password hanya jika diisi
$pw = trim($_POST['password_baru'] ?? '');
if (!empty($pw) && strlen($pw) >= 6) {
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    db_execute("UPDATE pengguna SET kata_sandi=? WHERE id_pengguna=?", "si", $hash, $id);
}

// Update session nama
$_SESSION['nama'] = $nama;

header("Location: profil.php?msg=updated");
exit;
