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

if (isset($_POST['submit'])) {
    // Menangkap data dari form di Canvas
    $id_pinjaman = mysqli_real_escape_string($koneksi, $_POST['id_pinjaman']);
    $tanggal_bayar = mysqli_real_escape_string($koneksi, $_POST['tanggal_bayar']);
    $jumlah_bayar = mysqli_real_escape_string($koneksi, $_POST['jumlah_bayar']); // Ini angka polos dari hidden input

    // 1. Ambil data sisa pinjaman saat ini sebelum dibayar
    $query_cek = mysqli_query($koneksi, "SELECT sisa_pinjaman FROM tb_pinjaman_ramdan WHERE id_pinjaman = '$id_pinjaman'");
    $data_pinjaman = mysqli_fetch_assoc($query_cek);
    
    if (!$data_pinjaman) {
        echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Data pinjaman tidak ditemukan!',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location='data_angsuran.php';
            });
        </script>";
        exit();
    }

    $sisa_lama = $data_pinjaman['sisa_pinjaman'];

    // 2. Hitung sisa pinjaman baru
    $sisa_baru = $sisa_lama - $jumlah_bayar;

    // Validasi keamanan: Sisa tidak boleh minus
    if ($sisa_baru < 0) {
        echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Gagal! Jumlah bayar melebihi sisa hutang.',
                icon: 'error',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit();
    }

    // 3. Simpan data ke tabel angsuran
    // Kolom disesuaikan: id_pinjaman, tanggal_bayar, jumlah_bayar (Tanpa metode_bayar)
    $sql_ins = "INSERT INTO tb_angsuran_ramdan (id_pinjaman, tanggal_bayar, jumlah_bayar) 
                VALUES ('$id_pinjaman', '$tanggal_bayar', '$jumlah_bayar')";
    
    $query_ins = mysqli_query($koneksi, $sql_ins);

    if ($query_ins) {
        // 4. Update sisa pinjaman di tabel pinjaman (Logika Utama)
        // Jika sisa baru adalah 0 atau kurang, status otomatis jadi 'Lunas'
        $status_baru = ($sisa_baru <= 0) ? 'Lunas' : 'Disetujui';
        
        $sql_upd = "UPDATE tb_pinjaman_ramdan SET 
                    sisa_pinjaman = '$sisa_baru', 
                    status_pinjaman = '$status_baru' 
                    WHERE id_pinjaman = '$id_pinjaman'";
        
        $query_upd = mysqli_query($koneksi, $sql_upd);

        if ($query_upd) {
            echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pembayaran Berhasil! Sisa pinjaman diperbarui.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location='data_angsuran.php';
                });
            </script>";
        } else {
            echo "Gagal Update Sisa Pinjaman: " . mysqli_error($koneksi);
        }
    } else {
        echo "Gagal Simpan Riwayat Angsuran: " . mysqli_error($koneksi);
    }
} else {
    // Jika diakses tanpa submit form
    header("Location: tambah_angsuran.php");
    exit();
}

echo "</body></html>";
?>