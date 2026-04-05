<?php
include 'auth.php';
include 'koneksi.php';

// biar admin yang bisa akses halaman ini, bukan petugas
if($_SESSION['role'] != 'Admin') {
    echo "Anda tidak memiliki akses!";
    exit;
}

$id = $_GET['id'];

// Query untuk mengubah status pinjaman
$query = mysqli_query($koneksi, "UPDATE tb_pinjaman_ramdan SET status_pinjaman='Disetujui' WHERE id_pinjaman='$id'");

if ($query) {
    header("Location: data_pinjaman.php");
} else {
    echo "Gagal menyetujui pinjaman: " . mysqli_error($koneksi);
}
?>