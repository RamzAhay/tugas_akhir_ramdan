<?php
/**
 * KONFIGURASI DATABASE & AUTO-INCLUDE FUNCTIONS
 */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_koperasi_ramdan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Set charset agar simbol mata uang dan teks aman
mysqli_set_charset($koneksi, "utf8mb4");

// OTOMATIS menyertakan functions.php di setiap file yang memanggil koneksi.php
include_once 'functions.php';
?>