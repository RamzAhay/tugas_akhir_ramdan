<?php
include 'auth.php';
include 'koneksi.php';

// Menangkap data dari form
$id_anggota     = $_POST['id_anggota'];
$jenis_simpanan = $_POST['jenis_simpanan'];
$jumlah         = $_POST['jumlah'];

// Catat tanggal transaksi otomatis ke hari ini
$tanggal = date('Y-m-d');

// Query insert ke tabel tb_simpanan_ramdan
$query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota, jenis_simpanan, jumlah, tanggal) 
                                 VALUES ('$id_anggota', '$jenis_simpanan', '$jumlah', '$tanggal')");

if ($query) {
    header("Location: data_simpanan.php");
} else {
    echo "Gagal mencatat transaksi: " . mysqli_error($koneksi);
}
?>