<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

/**
 * PROTEKSI KEAMANAN (RBAC):
 * Mengecek apakah user yang mencoba menghapus adalah Admin.
 * Jika bukan Admin, proses akan langsung ditolak dengan pesan kesalahan.
 */
if ($_SESSION['role'] != 'Admin') {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body{font-family: 'Poppins', sans-serif;}</style></head><body>";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak!',
            text: 'Berdasarkan aturan RBAC di Slide 3, Petugas tidak diizinkan menghapus Data Master Anggota.',
            confirmButtonColor: '#0d6efd'
        }).then(() => { window.location.href = 'data_anggota_ramdan.php'; });
    </script></body></html>";
    exit();
}

// Menangkap ID Anggota dari URL (parameter ?id=...)
if (isset($_GET['id'])) {
    $id_anggota = mysqli_real_escape_string($koneksi_ramdan, $_GET['id']);

    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body{font-family: 'Poppins', sans-serif;}</style></head><body>";

    /**
     * PROSES HAPUS:
     * Karena di database menggunakan ON DELETE CASCADE pada Foreign Key, 
     * menghapus anggota ini otomatis akan menghapus seluruh data simpanan dan pinjamannya.
     */
    $query = mysqli_query($koneksi_ramdan, "DELETE FROM tb_anggota_ramdan WHERE id_anggota_ramdan = '$id_anggota'");

    if ($query) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Data Berhasil Dihapus',
                text: 'Anggota dan seluruh riwayat transaksinya telah dihapus dari sistem.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => { window.location.href = 'data_anggota_ramdan.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: 'Terjadi kesalahan database: " . mysqli_error($koneksi_ramdan) . "',
                confirmButtonColor: '#d33'
            }).then(() => { window.history.back(); });
        </script>";
    }
    echo "</body></html>";
} else {
    // Jika tidak ada ID yang dikirim, lempar balik ke halaman data anggota
    header("Location: data_anggota_ramdan.php");
    exit();
}
?>