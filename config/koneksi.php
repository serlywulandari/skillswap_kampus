<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "skillswap_kampus";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");

// ─────────────────────────────────────────────────────────────
// db_query() — SELECT dengan prepared statement
// Contoh: db_query("SELECT * FROM tabel WHERE id=?", "i", $id)
// ─────────────────────────────────────────────────────────────
function db_query($sql, $types = "", ...$params) {
    global $koneksi;
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt) return false;
    if ($types && $params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

// ─────────────────────────────────────────────────────────────
// db_execute() — INSERT / UPDATE / DELETE dengan prepared statement
// Contoh: db_execute("UPDATE tabel SET x=? WHERE id=?", "si", $x, $id)
// ─────────────────────────────────────────────────────────────
function db_execute($sql, $types = "", ...$params) {
    global $koneksi;
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt) return false;
    if ($types && $params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// ─────────────────────────────────────────────────────────────
// db_count() — hitung jumlah baris dari query sederhana
// Contoh: db_count("SELECT id FROM tabel WHERE status='aktif'")
// ─────────────────────────────────────────────────────────────
function db_count($sql) {
    global $koneksi;
    $result = db_raw($sql);
    if (!$result) return 0;
    return mysqli_num_rows($result);
}

// ─────────────────────────────────────────────────────────────
// db_raw() — query langsung tanpa prepared statement (untuk
//            query statis yang tidak menerima input pengguna)
// Contoh: db_raw("SELECT * FROM keahlian ORDER BY nama_keahlian")
// ─────────────────────────────────────────────────────────────
function db_raw($sql) {
    global $koneksi;
    return mysqli_query($koneksi, $sql);
}

// ─────────────────────────────────────────────────────────────
// require_login() — cek session & level, redirect jika gagal
// ─────────────────────────────────────────────────────────────
function require_login($level = null) {
    if (!isset($_SESSION['id_pengguna'])) {
        header("Location: ../auth/login.php");
        exit;
    }
    if ($level && $_SESSION['level'] != $level) {
        header("Location: ../index.php");
        exit;
    }
}
?>
