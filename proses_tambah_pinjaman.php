<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    // Amankan input ID Anggota
    $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $tanggal_pinjaman = $_POST['tanggal_pinjaman'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $lama_pinjaman = $_POST['lama_pinjaman']; 
    
    // Ambil bunga dari form, default ke 10
    $bunga_persen = (isset($_POST['bunga']) && $_POST['bunga'] != '') ? $_POST['bunga'] : 10; 

    // =========================================================================
    // CRITICAL FIX: VALIDASI PINJAMAN AKTIF HARUS BERDASARKAN ID_ANGGOTA
    // =========================================================================
    $cek_aktif = mysqli_query($koneksi, "SELECT id_pinjaman FROM tb_pinjaman_ramdan 
                                         WHERE id_anggota = '$id_anggota' 
                                         AND status_pinjaman IN ('Diajukan', 'Disetujui')");
    
    if (mysqli_num_rows($cek_aktif) > 0) {
        echo "<script>alert('Gagal! Anggota ini (ID: $id_anggota) masih memiliki pinjaman yang aktif atau sedang menunggu persetujuan.'); window.history.back();</script>";
        exit();
    }

    // ==========================================
    // VALIDASI LIMIT 2: 3X TOTAL SIMPANAN
    // ==========================================
    $query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_saldo FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $data_simpanan = mysqli_fetch_assoc($query_simpanan);
    $total_simpanan = $data_simpanan['total_saldo'] ? $data_simpanan['total_saldo'] : 0;
    
    $limit_maksimal = $total_simpanan * 3;

    if ($jumlah_pinjaman > $limit_maksimal) {
        $limit_rp = "Rp " . number_format($limit_maksimal, 0, ',', '.');
        $simpanan_rp = "Rp " . number_format($total_simpanan, 0, ',', '.');
        echo "<script>alert('Gagal! Maksimal pinjaman adalah 3x total simpanan ($limit_rp). Saldo simpanan saat ini: $simpanan_rp'); window.history.back();</script>";
        exit();
    }

    // Perhitungan Bunga & Total
    $bunga_nominal = ($jumlah_pinjaman * $bunga_persen) / 100;
    $total_pinjaman = $jumlah_pinjaman + $bunga_nominal;
    $sisa_pinjaman = $total_pinjaman; 
    $status_pinjaman = 'Diajukan';

    $query = "INSERT INTO tb_pinjaman_ramdan 
              (id_anggota, tanggal_pinjaman, jumlah_pinjaman, bunga, total_pinjaman, sisa_pinjaman, lama_pinjaman, status_pinjaman) 
              VALUES 
              ('$id_anggota', '$tanggal_pinjaman', '$jumlah_pinjaman', '$bunga_persen', '$total_pinjaman', '$sisa_pinjaman', '$lama_pinjaman', '$status_pinjaman')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Pinjaman berhasil diajukan untuk anggota ID: $id_anggota!'); window.location='data_pinjaman.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("Location: tambah_pinjaman.php");
    exit();
}
?>