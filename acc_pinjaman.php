<?php
session_start(); // Tambahkan session_start() agar $_SESSION terbaca
include 'auth.php';
include 'koneksi.php';

// biar admin yang bisa akses halaman ini, bukan petugas
if($_SESSION['role'] != 'Admin') {
    echo "<script>
            alert('Akses Ditolak! Hanya Admin yang bisa melakukan ACC.');
            window.location.href='data_pinjaman.php';
          </script>";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "UPDATE tb_pinjaman_ramdan SET status_pinjaman='Disetujui' WHERE id_pinjaman='$id'");

if ($query) {
    echo "<script>
            alert('Pinjaman berhasil di-ACC! Status sekarang menjadi Belum Lunas.');
            window.location.href='data_pinjaman.php';
          </script>";
} else {
    echo "Gagal menyetujui pinjaman: " . mysqli_error($koneksi);
}
?>