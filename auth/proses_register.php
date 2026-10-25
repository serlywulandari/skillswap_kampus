<?php

session_start();
include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$nama     = trim($_POST['nama_lengkap'] ?? '');
$nim      = trim($_POST['nim'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['kata_sandi'] ?? '';
$fakultas = trim($_POST['fakultas'] ?? '');
$prodi    = trim($_POST['program_studi'] ?? '');

// Validasi dasar
if (empty($nama) || empty($nim) || empty($email) || empty($password)) {
    echo "<script>alert('Semua field wajib diisi!'); window.location='register.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Format email tidak valid!'); window.location='register.php';</script>";
    exit;
}

if (strlen($password) < 6) {
    echo "<script>alert('Password minimal 6 karakter!'); window.location='register.php';</script>";
    exit;
}

// Cek email duplikat
$cek_email = db_query(
    "SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1",
    "s", $email
);
if (mysqli_num_rows($cek_email) > 0) {
    echo "<script>alert('Email sudah terdaftar!'); window.location='register.php';</script>";
    exit;
}

// Cek NIM duplikat
$cek_nim = db_query(
    "SELECT id_pengguna FROM pengguna WHERE nim = ? LIMIT 1",
    "s", $nim
);
if (mysqli_num_rows($cek_nim) > 0) {
    echo "<script>alert('NIM sudah terdaftar!'); window.location='register.php';</script>";
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

$ok = db_execute(
    "INSERT INTO pengguna (nama_lengkap, nim, email, kata_sandi, fakultas, program_studi, level)
     VALUES (?, ?, ?, ?, ?, ?, 'mahasiswa')",
    "ssssss", $nama, $nim, $email, $hash, $fakultas, $prodi
);

if ($ok) {
    echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
} else {
    echo "<script>alert('Registrasi gagal, coba lagi.'); window.location='register.php';</script>";
}
?>
