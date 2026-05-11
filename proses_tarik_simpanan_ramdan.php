<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

if (isset($_POST['submit'])) {
    $id_anggota_ramdan   = mysqli_real_escape_string($koneksi, $_POST['id_anggota_ramdan']);
    $jumlah_tarik = (int)$_POST['jumlah_tarik']; // Ambil angka murni
    $tanggal_ramdan      = $_POST['tanggal_ramdan'];
    $metode       = isset($_POST['metode_pembayaran_ramdan']) ? $_POST['metode_pembayaran_ramdan'] : 'Tunai';

    // Persiapan SweetAlert
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='font-family:Poppins;'>";

    // 1. CEK SALDO LAGI DI SISI SERVER (Penting!)
    $q_saldo = mysqli_query($koneksi, "SELECT SUM(jumlah_ramdan) as total FROM tb_simpanan_ramdan WHERE id_anggota_ramdan = '$id_anggota_ramdan'");
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
    $query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota_ramdan, jenis_simpanan_ramdan, jumlah_ramdan, tanggal_ramdan, metode_pembayaran_ramdan) 
                                     VALUES ('$id_anggota_ramdan', 'Sukarela', '-$jumlah_tarik', '$tanggal_ramdan', '$metode')");

    if ($query) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Penarikan tunai sebesar " . rupiah($jumlah_tarik) . " berhasil dicatat.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => { window.location.href = 'data_simpanan_ramdan.php'; });
        </script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
    echo "</body></html>";
}
?>