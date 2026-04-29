<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $id_anggota   = $_POST['id_anggota'];
    $jumlah_tarik = $_POST['jumlah_tarik'];
    $tanggal      = $_POST['tanggal'];

    // Logika: Kita masukkan sebagai "Sukarela" namun dengan nilai NEGATIF (-)
    // Agar saat dijumlahkan (SUM), saldo otomatis berkurang
    $query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota, jenis_simpanan, jumlah, tanggal) 
                                     VALUES ('$id_anggota', 'Sukarela', '-$jumlah_tarik', '$tanggal')");

    if ($query) {
        echo "<script>alert('Penarikan Berhasil Dicatat!'); window.location='data_simpanan.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>