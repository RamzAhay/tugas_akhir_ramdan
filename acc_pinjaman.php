<?php
session_start(); // Tambahkan session_start() agar $_SESSION terbaca
include 'auth.php';
include 'koneksi.php';

// Include SweetAlert2 library
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// =========================================================================
// WADAH HTML UNTUK SWEETALERT (Agar layar tidak blank saat alert muncul)
// =========================================================================
echo "<!DOCTYPE html><html><head>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "<style>body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }</style>";
echo "</head><body>";

// biar admin yang bisa akses halaman ini, bukan petugas
if($_SESSION['role'] != 'Admin') {
    echo "<script>
            Swal.fire({
                title: 'Akses Ditolak!',
                text: 'Hanya Admin yang bisa melakukan ACC.',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href='data_pinjaman.php';
            });
          </script>";
    echo "</body></html>";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "UPDATE tb_pinjaman_ramdan SET status_pinjaman='Disetujui' WHERE id_pinjaman='$id'");

if ($query) {
    echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pinjaman berhasil di-ACC! Status sekarang menjadi Belum Lunas.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href='data_pinjaman.php';
            });
          </script>";
} else {
    echo "Gagal menyetujui pinjaman: " . mysqli_error($koneksi);
}
?>