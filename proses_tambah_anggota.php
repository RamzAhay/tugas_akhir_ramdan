<?php
include 'auth.php';
include 'koneksi.php';

$nama   = $_POST['nama'];
$alamat = $_POST['alamat'];
$no_hp  = $_POST['no_hp'];

// atur tanggal daftar otomatis
$tanggal_daftar = date('Y-m-d'); 

$query = mysqli_query($koneksi, "INSERT INTO tb_anggota_ramdan (nama, alamat, no_hp, tanggal_daftar) 
                                 VALUES ('$nama', '$alamat', '$no_hp', '$tanggal_daftar')");

if ($query) {
    header("Location: data_anggota.php");
} else {
    echo "Gagal menambah data anggota: " . mysqli_error($koneksi);
}
?>