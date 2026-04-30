<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan hanya Admin yang bisa menolak pinjaman
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang dapat memproses penolakan.'); window.history.back();</script>";
    exit();
}

// Menangkap ID Pinjaman dari URL
if (isset($_GET['id'])) {
    $id_pinjaman = $_GET['id'];

    // Update status pinjaman menjadi 'Ditolak'
    $query = "UPDATE tb_pinjaman_ramdan SET status_pinjaman = 'Ditolak' WHERE id_pinjaman = '$id_pinjaman'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Pengajuan Pinjaman Berhasil Ditolak!'); window.location='data_pinjaman.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($koneksi) . "'); window.location='data_pinjaman.php';</script>";
    }
} else {
    header("Location: data_pinjaman.php");
    exit();
}
?>