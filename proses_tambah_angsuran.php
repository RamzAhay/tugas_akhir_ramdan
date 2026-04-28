<?php
include 'auth.php';
include 'koneksi.php';

$id_pinjaman  = $_POST['id_pinjaman'];
$jumlah_bayar = $_POST['jumlah_bayar'];
$tanggal      = date('Y-m-d');

// 1. Ambil data total pinjaman dari tabel pinjaman
$query_pinjaman = mysqli_query($koneksi, "SELECT total_pinjaman FROM tb_pinjaman_ramdan WHERE id_pinjaman='$id_pinjaman'");
$data_pinjaman  = mysqli_fetch_assoc($query_pinjaman);
$total_hutang   = $data_pinjaman['total_pinjaman'];

// 2. Hitung berapa total yang sudah dibayar sebelumnya untuk pinjaman ini
$query_sudah_bayar = mysqli_query($koneksi, "SELECT SUM(jumlah_bayar) as total_dibayar FROM tb_angsuran_ramdan WHERE id_pinjaman='$id_pinjaman'");
$data_sudah_bayar  = mysqli_fetch_assoc($query_sudah_bayar);
$total_dibayar     = $data_sudah_bayar['total_dibayar'] ? $data_sudah_bayar['total_dibayar'] : 0;

// ==============================================================
// BLOK VALIDASI ANTI-KELEBIHAN BAYAR (TAMBAHAN BARU)
// ==============================================================
$sisa_hutang_sebelumnya = $total_hutang - $total_dibayar;

if ($jumlah_bayar > $sisa_hutang_sebelumnya) {
    echo "<script>
            alert('GAGAL! Nominal bayar (Rp " . number_format($jumlah_bayar,0,',','.') . ") melebihi sisa hutang (Rp " . number_format($sisa_hutang_sebelumnya,0,',','.') . "). Sisa hutang tidak boleh minus!');
            window.location.href = 'tambah_angsuran.php';
          </script>";
    exit(); // Proses dimatikan di sini agar query insert di bawah tidak dijalankan
}
// ==============================================================

// 3. Hitung sisa pinjaman setelah pembayaran ini
$sisa_pinjaman = $total_hutang - ($total_dibayar + $jumlah_bayar);

// 4. Masukkan data pembayaran ke tabel angsuran
$query_insert = mysqli_query($koneksi, "INSERT INTO tb_angsuran_ramdan (id_pinjaman, jumlah_bayar, sisa_pinjaman, tanggal_bayar) 
                                        VALUES ('$id_pinjaman', '$jumlah_bayar', '$sisa_pinjaman', '$tanggal')");

if ($query_insert) {
    // 5. CEK APAKAH SUDAH LUNAS?
    if ($sisa_pinjaman <= 0) {
        // Update status pinjaman jadi Lunas
        mysqli_query($koneksi, "UPDATE tb_pinjaman_ramdan SET status_pinjaman='Lunas' WHERE id_pinjaman='$id_pinjaman'");
    }
    header("Location: data_angsuran.php");
} else {
    echo "Gagal memproses angsuran: " . mysqli_error($koneksi);
}
?>