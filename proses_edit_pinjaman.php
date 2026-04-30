<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $id_pinjaman = $_POST['id_pinjaman'];
    $tanggal_pinjaman = $_POST['tanggal_pinjaman'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $lama_pinjaman = $_POST['lama_pinjaman']; 
    $bunga_persen = $_POST['bunga']; 

    // Ambil ID Anggota dari pinjaman yang sedang diedit
    $query_pinjaman = mysqli_query($koneksi, "SELECT id_anggota FROM tb_pinjaman_ramdan WHERE id_pinjaman = '$id_pinjaman'");
    $data_pinjaman = mysqli_fetch_assoc($query_pinjaman);
    $id_anggota = $data_pinjaman['id_anggota'];

    // Hitung total simpanan anggota tersebut
    $query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_saldo FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $data_simpanan = mysqli_fetch_assoc($query_simpanan);
    $total_simpanan = $data_simpanan['total_saldo'] ? $data_simpanan['total_saldo'] : 0;

    // Validasi Limit Maksimal Dinamis (3x Total Simpanan)
    $limit_maksimal = $total_simpanan * 3;

    if ($jumlah_pinjaman > $limit_maksimal) {
        $limit_rp = "Rp " . number_format($limit_maksimal, 0, ',', '.');
        $simpanan_rp = "Rp " . number_format($total_simpanan, 0, ',', '.');
        echo "<script>alert('Gagal! Maksimal pengajuan pinjaman adalah 3x total simpanan ($limit_rp). Total simpanan anggota saat ini: $simpanan_rp'); window.history.back();</script>";
        exit();
    }

    // Hitung ulang karena nominal atau bunga bisa saja diubah
    $bunga_nominal = ($jumlah_pinjaman * $bunga_persen) / 100;
    $total_pinjaman = $jumlah_pinjaman + $bunga_nominal;
    // Sisa pinjaman mengikuti total karena belum ada angsuran (statusnya masih diajukan)
    $sisa_pinjaman = $total_pinjaman;

    $query = "UPDATE tb_pinjaman_ramdan SET 
              tanggal_pinjaman = '$tanggal_pinjaman',
              jumlah_pinjaman = '$jumlah_pinjaman',
              bunga = '$bunga_persen',
              lama_pinjaman = '$lama_pinjaman',
              total_pinjaman = '$total_pinjaman',
              sisa_pinjaman = '$sisa_pinjaman'
              WHERE id_pinjaman = '$id_pinjaman' AND status_pinjaman = 'Diajukan'";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Data Pengajuan Pinjaman Berhasil Diperbarui!'); window.location='data_pinjaman.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
} else {
    header("Location: data_pinjaman.php");
    exit();
}
?>