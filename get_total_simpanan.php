<?php
include 'koneksi.php';

if (isset($_POST['id_anggota'])) {
    $id_anggota = $_POST['id_anggota'];

    // Hitung total saldo simpanan (Jumlah Masuk - Jumlah Keluar)
    $query = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_saldo FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $data = mysqli_fetch_assoc($query);
    
    $saldo = $data['total_saldo'] ? $data['total_saldo'] : 0;

    // Kirim data saldo mentah agar bisa diolah JavaScript
    echo $saldo;
}
?>