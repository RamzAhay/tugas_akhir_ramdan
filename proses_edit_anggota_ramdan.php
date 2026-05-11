<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

$id_anggota_ramdan = $_POST['id_anggota_ramdan'];
$nama_ramdan       = $_POST['nama_ramdan'];
$alamat_ramdan     = $_POST['alamat_ramdan'];
$no_hp_ramdan      = $_POST['no_hp_ramdan'];

$query = mysqli_query($koneksi, "UPDATE tb_anggota_ramdan SET 
                                 nama_ramdan='$nama_ramdan', 
                                 alamat_ramdan='$alamat_ramdan', 
                                 no_hp_ramdan='$no_hp_ramdan' 
                                 WHERE id_anggota_ramdan='$id_anggota_ramdan'");

if ($query) {
    header("Location: data_anggota_ramdan.php");
} else {
    echo "Gagal mengupdate data: " . mysqli_error($koneksi);
}
?>