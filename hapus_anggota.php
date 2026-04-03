<?php
include 'auth.php';
include 'koneksi.php';

// Menangkap ID dari URL
$id = $_GET['id'];

// Query untuk menghapus data
$query = mysqli_query($koneksi, "DELETE FROM tb_anggota_ramdan WHERE id_anggota='$id'");

if ($query) {
    header("Location: data_anggota.php");
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>