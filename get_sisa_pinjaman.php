<?php
// Pastikan file koneksi ke database disertakan
include 'koneksi.php';

if (isset($_POST['id_pinjaman'])) {
    $id_pinjaman = $_POST['id_pinjaman'];

    // 1. Ambil data pinjaman berdasarkan ID
    $query_pinjaman = mysqli_query($koneksi, "SELECT jumlah_pinjaman FROM tb_pinjaman_ramdan WHERE id_pinjaman = '$id_pinjaman'");
    $data_pinjaman = mysqli_fetch_assoc($query_pinjaman);
    
    $pokok_pinjaman = $data_pinjaman['jumlah_pinjaman'];

    // 2. Hitung Bunga (Misalnya 5%)
    $bunga = 5 / 100; // 5 Persen
    $total_bunga = $pokok_pinjaman * $bunga;

    // 3. Hitung Total Hutang (Pokok + Bunga)
    $total_hutang = $pokok_pinjaman + $total_bunga; // 500.000 + 25.000 = 525.000

    // 4. Ambil total jumlah yang sudah dibayar (diangsur) dari tabel angsuran
    $query_angsuran = mysqli_query($koneksi, "SELECT SUM(jumlah_bayar) AS total_dibayar FROM tb_angsuran_ramdan WHERE id_pinjaman = '$id_pinjaman'");
    $data_angsuran = mysqli_fetch_assoc($query_angsuran);
    
    // Jika belum ada yang dibayar, set jadi 0
    $sudah_dibayar = $data_angsuran['total_dibayar'] ? $data_angsuran['total_dibayar'] : 0;

    // 5. Hitung Sisa Hutang
    $sisa_hutang = $total_hutang - $sudah_dibayar;

    // 6. Format ke Rupiah dan kirim kembali sebagai JSON agar diterima oleh AJAX JavaScript kamu
    $response = array(
        'total_pinjaman' => 'Rp ' . number_format($total_hutang, 0, ',', '.'),
        'sudah_dibayar'  => 'Rp ' . number_format($sudah_dibayar, 0, ',', '.'),
        'sisa_pinjaman'  => 'Rp ' . number_format($sisa_hutang, 0, ',', '.')
    );

    echo json_encode($response);
}
?>