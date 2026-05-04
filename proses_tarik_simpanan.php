<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $id_anggota   = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $jumlah_tarik = (int)$_POST['jumlah_tarik']; // Ambil angka murni
    $tanggal      = $_POST['tanggal'];
    $metode       = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : 'Tunai';

    // Persiapan SweetAlert
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='font-family:Poppins;'>";

    // 1. CEK SALDO LAGI DI SISI SERVER (Penting!)
    $q_saldo = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan WHERE id_anggota = '$id_anggota'");
    $d_saldo = mysqli_fetch_assoc($q_saldo);
    $saldo_saat_ini = $d_saldo['total'] ?? 0;

    if ($jumlah_tarik > $saldo_saat_ini) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Transaksi Gagal',
                text: 'Maaf, saldo anggota tidak mencukupi untuk penarikan ini.',
                confirmButtonColor: '#d33'
            }).then(() => { window.history.back(); });
        </script>";
        exit();
    }

    if ($jumlah_tarik <= 0) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Valid',
                text: 'Nominal penarikan harus lebih dari nol.',
                confirmButtonColor: '#3085d6'
            }).then(() => { window.history.back(); });
        </script>";
        exit();
    }

    // 2. Jika lolos validasi, masukkan data penarikan (nilai negatif)
    $query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota, jenis_simpanan, jumlah, tanggal, metode_pembayaran) 
                                     VALUES ('$id_anggota', 'Sukarela', '-$jumlah_tarik', '$tanggal', '$metode')");

    if ($query) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Penarikan tunai sebesar " . rupiah($jumlah_tarik) . " berhasil dicatat.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => { window.location.href = 'data_simpanan.php'; });
        </script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
    echo "</body></html>";
}
?>