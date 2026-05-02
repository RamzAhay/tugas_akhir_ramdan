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
    $id_anggota   = $_POST['id_anggota'];
    $jumlah_tarik = $_POST['jumlah_tarik'];
    $tanggal      = $_POST['tanggal'];

    // Logika: Kita masukkan sebagai "Sukarela" namun dengan nilai NEGATIF (-)
    // Agar saat dijumlahkan (SUM), saldo otomatis berkurang
    $query = mysqli_query($koneksi, "INSERT INTO tb_simpanan_ramdan (id_anggota, jenis_simpanan, jumlah, tanggal) 
                                     VALUES ('$id_anggota', 'Sukarela', '-$jumlah_tarik', '$tanggal')");

    if ($query) {
        echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Penarikan Berhasil Dicatat!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='data_simpanan.php';
            });
        </script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}

echo "</body></html>";
?>