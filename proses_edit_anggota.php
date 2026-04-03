<?php
include 'auth.php';
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$id_anggota = $_POST['id_anggota'];
$nama       = $_POST['nama'];
$alamat     = $_POST['alamat'];
$no_hp      = $_POST['no_hp'];

// Query untuk update data
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