<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $id_anggota = $_POST['id_anggota'];
    $tanggal_pinjaman = $_POST['tanggal_pinjaman'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $lama_pinjaman = $_POST['lama_pinjaman']; 
    $bunga_persen = isset($_POST['bunga']) ? $_POST['bunga'] : 10; 

    // ==========================================
    // BUG FIX 1: LIMIT PINJAMAN AKTIF (Hanya boleh 1)
    // ==========================================
    $cek_aktif = mysqli_query($koneksi, "SELECT id_pinjaman FROM tb_pinjaman_ramdan 
                                         WHERE id_anggota = '$id_anggota' 
                                         AND status_pinjaman IN ('Diajukan', 'Disetujui')");
    if (mysqli_num_rows($cek_aktif) > 0) {
        echo "<script>alert('Gagal! Anggota ini masih memiliki pinjaman yang sedang berjalan atau dalam proses pengajuan. Lunasi terlebih dahulu!'); window.history.back();</script>";
        exit();
    }

    // ==========================================
    // BUG FIX 2: LIMIT PINJAMAN (3x Total Simpanan)
    // ==========================================
    // Hitung total simpanan anggota tersebut
    $query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_saldo FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $data_simpanan = mysqli_fetch_assoc($query_simpanan);
    
    // Jika belum punya simpanan, set saldo 0
    $total_simpanan = $data_simpanan['total_saldo'] ? $data_simpanan['total_saldo'] : 0;

    // Validasi Limit Maksimal Dinamis (3x Total Simpanan)
    $limit_maksimal = $total_simpanan * 3;

    if ($jumlah_pinjaman > $limit_maksimal) {
        $limit_rp = "Rp " . number_format($limit_maksimal, 0, ',', '.');
        $simpanan_rp = "Rp " . number_format($total_simpanan, 0, ',', '.');
        echo "<script>alert('Gagal! Maksimal pengajuan pinjaman adalah 3x total simpanan ($limit_rp). Total simpanan anggota saat ini: $simpanan_rp'); window.history.back();</script>";
        exit();
    }

    // ==========================================
    // PERHITUNGAN DAN SIMPAN KE DATABASE
    // ==========================================
    $bunga_nominal = ($jumlah_pinjaman * $bunga_persen) / 100;
    $total_pinjaman = $jumlah_pinjaman + $bunga_nominal;
    
    // Sisa HARUS SAMA dengan Total saat pertama kali pinjam
    $sisa_pinjaman = $total_pinjaman;
    
    // Default status saat baru input
    $status_pinjaman = 'Diajukan';

    $query = "INSERT INTO tb_pinjaman_ramdan 
              (id_anggota, tanggal_pinjaman, jumlah_pinjaman, bunga, total_pinjaman, sisa_pinjaman, lama_pinjaman, status_pinjaman) 
              VALUES 
              ('$id_anggota', '$tanggal_pinjaman', '$jumlah_pinjaman', '$bunga_persen', '$total_pinjaman', '$sisa_pinjaman', '$lama_pinjaman', '$status_pinjaman')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Pengajuan Pinjaman Berhasil Disimpan!'); window.location='data_pinjaman.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
} else {
    // Jika file diakses langsung tanpa lewat form
    header("Location: tambah_pinjaman.php");
    exit();
}
?>