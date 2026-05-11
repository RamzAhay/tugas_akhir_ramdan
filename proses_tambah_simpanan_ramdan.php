<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

if (isset($_POST['submit'])) {
    $id_anggota_ramdan = mysqli_real_escape_string($koneksi, $_POST['id_anggota_ramdan']);
    $jenis_simpanan_ramdan = mysqli_real_escape_string($koneksi, $_POST['jenis_simpanan_ramdan']);
    $jumlah_ramdan = mysqli_real_escape_string($koneksi, $_POST['jumlah_ramdan']);
    $tanggal_ramdan = mysqli_real_escape_string($koneksi, $_POST['tanggal_ramdan']);
    $metode_pembayaran_ramdan = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran_ramdan']);

    // Setup HTML untuk efek loading SweetAlert agar layar tidak putih (blank)
    echo "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }</style></head><body>";

    // Insert ke tabel (Termasuk kolom metode_pembayaran_ramdan)
    $query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota_ramdan, jenis_simpanan_ramdan, jumlah_ramdan, tanggal_ramdan, metode_pembayaran_ramdan) 
                                     VALUES ('$id_anggota_ramdan', '$jenis_simpanan_ramdan', '$jumlah_ramdan', '$tanggal_ramdan', '$metode_pembayaran_ramdan')");

    if ($query) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data simpanan berhasil dicatat.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'data_simpanan_ramdan.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem',
                text: 'Gagal menyimpan data: " . mysqli_error($koneksi) . "',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    echo "</body></html>";
} else {
    header("Location: tambah_simpanan_ramdan.php");
    exit();
}
?>