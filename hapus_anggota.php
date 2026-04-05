<?php
include 'auth.php';
include 'koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($koneksi, "DELETE FROM tb_anggota_ramdan WHERE id_anggota='$id'");

if ($query) {
    header("Location: data_anggota.php");
} else {
    echo "Gagal menghapus data: " . mysqli_error($koneksi);
}
?>