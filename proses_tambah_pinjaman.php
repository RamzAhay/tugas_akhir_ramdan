<?php
include 'auth.php';
include 'koneksi.php';

$id_anggota      = $_POST['id_anggota'];
$jumlah_pinjaman = $_POST['jumlah_pinjaman'];
$bunga           = $_POST['bunga'];
$lama_pinjaman   = $_POST['lama_pinjaman'];

// PERHITUNGAN MATEMATIKA:
// Hitung nominal bunga dari persen
$nominal_bunga = $jumlah_pinjaman * ($bunga / 100);

// Total hutang = Pinjaman awal + nominal bunga
$total_pinjaman = $jumlah_pinjaman + $nominal_bunga;

$tanggal = date('Y-m-d');
$status = 'Diajukan'; // Default status saat baru input

$query = mysqli_query($koneksi, "INSERT INTO tb_pinjaman_ramdan 
                                 (id_anggota, jumlah_pinjaman, bunga, lama_pinjaman, total_pinjaman, status_pinjaman, tanggal_pinjaman) 
                                 VALUES 
                                 ('$id_anggota', '$jumlah_pinjaman', '$bunga', '$lama_pinjaman', '$total_pinjaman', '$status', '$tanggal')");

if ($query) {
    header("Location: data_pinjaman.php");
} else {
    echo "Gagal mengajukan pinjaman: " . mysqli_error($koneksi);
}
?>