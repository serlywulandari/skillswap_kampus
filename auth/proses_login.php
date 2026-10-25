<?php

session_start();
include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['kata_sandi'] ?? '';

if (empty($email) || empty($password)) {
    echo "<script>alert('Email dan password wajib diisi!'); window.location='login.php';</script>";
    exit;
}

// Ambil user berdasarkan email saja, lalu verifikasi password
$result = db_query(
    "SELECT * FROM pengguna WHERE email = ? LIMIT 1",
    "s", $email
);

$data = $result ? mysqli_fetch_assoc($result) : null;

// Dukung password lama (plaintext) dan baru (hashed)
$valid = false;
if ($data) {
    if (password_verify($password, $data['kata_sandi'])) {
        $valid = true;
    } elseif ($data['kata_sandi'] === $password) {
        // Password lama plaintext — upgrade ke hash sekarang
        $hash = password_hash($password, PASSWORD_DEFAULT);
        db_execute(
            "UPDATE pengguna SET kata_sandi = ? WHERE id_pengguna = ?",
            "si", $hash, $data['id_pengguna']
        );
        $valid = true;
    }
}

if ($valid) {
    session_regenerate_id(true);
    $_SESSION['id_pengguna'] = $data['id_pengguna'];
    $_SESSION['nama']        = $data['nama_lengkap'];
    $_SESSION['level']       = $data['level'];

    if ($data['level'] == "admin") {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../mahasiswa/dashboard.php");
    }
    exit;
} else {
    echo "<script>alert('Email atau Password salah!'); window.location='login.php';</script>";
}
?>
