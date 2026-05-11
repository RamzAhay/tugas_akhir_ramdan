<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

$nama_ramdan   = $_POST['nama_ramdan'];
$alamat_ramdan = $_POST['alamat_ramdan'];
$no_hp_ramdan  = $_POST['no_hp_ramdan'];

// atur tanggal_ramdan daftar otomatis
$tanggal_daftar_ramdan = date('Y-m-d'); 

$query = mysqli_query($koneksi, "INSERT INTO tb_anggota_ramdan (nama_ramdan, alamat_ramdan, no_hp_ramdan, tanggal_daftar_ramdan) 
                                 VALUES ('$nama_ramdan', '$alamat_ramdan', '$no_hp_ramdan', '$tanggal_daftar_ramdan')");

if ($query) {
    header("Location: data_anggota_ramdan.php");
} else {
    echo "Gagal menambah data anggota: " . mysqli_error($koneksi);
}
?>