<?php
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

// Pastikan hanya Admin yang bisa menolak pinjaman
if ($_SESSION['role'] != 'Admin') {
    echo "<script>
        Swal.fire({
            title: 'Akses Ditolak!',
            text: 'Hanya Admin yang dapat memproses penolakan.',
            icon: 'warning',
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.history.back();
        });
    </script>";
    exit();
}

// Menangkap ID Pinjaman dari URL
if (isset($_GET['id'])) {
    $id_pinjaman = $_GET['id'];

    // Update status pinjaman menjadi 'Ditolak'
    $query = "UPDATE tb_pinjaman_ramdan SET status_pinjaman = 'Ditolak' WHERE id_pinjaman = '$id_pinjaman'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pengajuan Pinjaman Berhasil Ditolak!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='data_pinjaman.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan: " . mysqli_error($koneksi) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location='data_pinjaman.php';
            });
        </script>";
    }
} else {
    header("Location: data_pinjaman.php");
    exit();
}

echo "</body></html>";
?>