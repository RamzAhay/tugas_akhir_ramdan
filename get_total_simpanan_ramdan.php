<?php
include 'koneksi_ramdan.php';

if (isset($_POST['id_anggota_ramdan'])) {
    $id_anggota_ramdan = $_POST['id_anggota_ramdan'];

    // Hitung total saldo simpanan (Jumlah Masuk - Jumlah Keluar)
    $query = mysqli_query($koneksi, "SELECT SUM(jumlah_ramdan) as total_saldo FROM tb_simpanan_ramdan WHERE id_anggota_ramdan = '$id_anggota_ramdan'");
    $data = mysqli_fetch_assoc($query);
    
    $saldo = $data['total_saldo'] ? $data['total_saldo'] : 0;

    // Kirim data saldo mentah agar bisa diolah JavaScript
    echo $saldo;
}
?>