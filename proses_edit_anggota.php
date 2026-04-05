<?php
include 'auth.php';
include 'koneksi.php';

$id_anggota = $_POST['id_anggota'];
$nama       = $_POST['nama'];
$alamat     = $_POST['alamat'];
$no_hp      = $_POST['no_hp'];

$query = mysqli_query($koneksi, "UPDATE tb_anggota_ramdan SET 
                                 nama='$nama', 
                                 alamat='$alamat', 
                                 no_hp='$no_hp' 
                                 WHERE id_anggota='$id_anggota'");

if ($query) {
    header("Location: data_anggota.php");
} else {
    echo "Gagal mengupdate data: " . mysqli_error($koneksi);
}
?>