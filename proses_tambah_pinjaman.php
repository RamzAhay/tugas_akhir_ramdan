<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data dari form
    $id_anggota      = $_POST['id_anggota'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $bunga           = $_POST['bunga'];
    $lama_pinjaman   = $_POST['lama_pinjaman'];

    // --- HITUNG TOTAL PINJAMAN OTOMATIS ---
    $nominal_bunga = ($jumlah_pinjaman * $bunga) / 100;
    $total_pinjaman = $jumlah_pinjaman + $nominal_bunga;

    // Set tanggal hari ini sebagai tanggal pinjam
    $tgl_pinjam      = date('Y-m-d');

    // 2. HITUNG TOTAL SIMPANAN ANGGOTA
    $query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_simpanan FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $data_simpanan = mysqli_fetch_assoc($query_simpanan);

    // Jika data simpanan kosong, set jadi 0
    $total_simpanan = $data_simpanan['total_simpanan'] ? $data_simpanan['total_simpanan'] : 0;

    // 3. TENTUKAN ATURAN MAKSIMAL PINJAMAN
    // Koperasi mengizinkan pinjaman maksimal 3x lipat dari total simpanan
    $pengali = 3;
    $max_pinjaman = $total_simpanan * $pengali;

    // 4. VALIDASI PENGAJUAN PINJAMAN
    if ($jumlah_pinjaman > $max_pinjaman) {
        // Jika jumlah pinjaman melebih batas, batalkan dan tampilkan pesan error
        echo "<script>
                alert('GAGAL! Maksimal pinjaman untuk anggota ini adalah Rp " . number_format($max_pinjaman, 0, ',', '.') . "\\n(Total Simpanannya saat ini: Rp " . number_format($total_simpanan, 0, ',', '.') . ")');
                window.location.href='tambah_pinjaman.php';
              </script>";
    } else {
        // --- KEMBALIKAN KE 'Diajukan' SESUAI DATABASE ---
        $query_insert = "INSERT INTO tb_pinjaman_ramdan (id_anggota, jumlah_pinjaman, bunga, lama_pinjaman, total_pinjaman, tanggal_pinjaman, status_pinjaman) 
                 VALUES ('$id_anggota', '$jumlah_pinjaman', '$bunga', '$lama_pinjaman', '$total_pinjaman', '$tgl_pinjam', 'Diajukan')";

        if (mysqli_query($koneksi, $query_insert)) {
            echo "<script>
                    alert('Pengajuan Pinjaman berhasil! Menunggu ACC dari Admin.');
                    window.location.href='data_pinjaman.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Terjadi kesalahan saat menyimpan ke database!');
                    window.location.href='tambah_pinjaman.php';
                  </script>";
        }
    }
}
