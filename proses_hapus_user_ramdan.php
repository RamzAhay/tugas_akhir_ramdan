<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

if ($_SESSION['role'] != 'Admin') {
    die("Akses Ditolak!");
}

if (isset($_GET['id'])) {
    $id_user_ramdan = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Keamanan lapis dua: Admin tidak boleh hapus diri sendiri lewat URL injeksi
    if ($id_user_ramdan == $_SESSION['id_user_ramdan']) {
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!'); window.location='data_user_ramdan.php';</script>";
        exit();
    }

    $query = mysqli_query($koneksi, "DELETE FROM tb_user_ramdan WHERE id_user_ramdan = '$id_user_ramdan'");

    if ($query) {
        echo "<script>alert('Akses pengguna berhasil dicabut!'); window.location='data_user_ramdan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna.'); window.location='data_user_ramdan.php';</script>";
    }
} else {
    header("Location: data_user_ramdan.php");
}
?>