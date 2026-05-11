<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

// Include SweetAlert2 library
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Jika form tidak disubmit melalui tombol, kembalikan ke form
if (!isset($_POST['submit'])) {
    header("Location: tambah_pinjaman_ramdan.php");
    exit();
}

// 1. Ambil & Amankan Data Form
$id_anggota_ramdan = mysqli_real_escape_string($koneksi, $_POST['id_anggota_ramdan']);
$tanggal_pinjaman_ramdan = $_POST['tanggal_pinjaman_ramdan'];
$lama_pinjaman_ramdan = $_POST['lama_pinjaman_ramdan']; 
$bunga_persen = (isset($_POST['bunga_ramdan']) && $_POST['bunga_ramdan'] != '') ? $_POST['bunga_ramdan'] : 10; 

// Menghilangkan format rupiah (titik/koma) sebelum masuk ke database
$jumlah_pinjaman_kotor = $_POST['jumlah_pinjaman_ramdan'];
$jumlah_pinjaman_ramdan = (int) preg_replace('/\D/', '', $jumlah_pinjaman_kotor);

// =========================================================================
// WADAH HTML UNTUK SWEETALERT (Agar layar tidak blank saat alert muncul)
// =========================================================================
echo "<!DOCTYPE html><html><head>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "<style>body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }</style>";
echo "</head><body>";


// 2. Validasi Pinjaman Aktif
$cek_aktif = mysqli_query($koneksi, "SELECT id_pinjaman_ramdan FROM tb_pinjaman_ramdan 
                                     WHERE id_anggota_ramdan = '$id_anggota_ramdan' 
                                     AND status_pinjaman_ramdan IN ('Diajukan', 'Disetujui')");

if (mysqli_num_rows($cek_aktif) > 0) {
    echo "<script>
        Swal.fire({
            title: 'Pinjaman Ditolak!',
            text: 'Anggota ini masih memiliki pinjaman yang aktif atau sedang menunggu persetujuan.',
            icon: 'warning',
            confirmButtonText: 'Kembali',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.back();
        });
    </script></body></html>";
    exit();
}

// 3. Validasi Syarat Saldo Simpanan (Minimal 20% dari Pinjaman)
$q_saldo = mysqli_query($koneksi, "SELECT SUM(jumlah_ramdan) as saldo FROM tb_simpanan_ramdan WHERE id_anggota_ramdan = '$id_anggota_ramdan'");
$d_saldo = mysqli_fetch_assoc($q_saldo);
$total_simpanan = $d_saldo['saldo'] ?? 0;

$syarat_minimal = $jumlah_pinjaman_ramdan * 0.20;

if ($total_simpanan < $syarat_minimal) {
    $syarat_rp = rupiah($syarat_minimal);
    $simpanan_rp = rupiah($total_simpanan);
    
    echo "<script>
        Swal.fire({
            title: 'Saldo Tidak Memenuhi Syarat!',
            html: 'Pinjaman membutuhkan minimal 20% saldo simpanan.<br><br>Syarat Saldo: <strong>$syarat_rp</strong><br>Saldo saat ini: <strong>$simpanan_rp</strong>',
            icon: 'error',
            confirmButtonText: 'Kembali ke Form',
            confirmButtonColor: '#0d6efd'
        }).then(() => {
            window.history.back();
        });
    </script></body></html>";
    exit();
}

// 4. Perhitungan Bunga & Total
$bunga_nominal = ($jumlah_pinjaman_ramdan * $bunga_persen) / 100;
$total_pinjaman_ramdan = $jumlah_pinjaman_ramdan + $bunga_nominal;
$sisa_pinjaman_ramdan = $total_pinjaman_ramdan; 
$status_pinjaman_ramdan = 'Diajukan';

// 5. Eksekusi Query Insert
$query = "INSERT INTO tb_pinjaman_ramdan 
          (id_anggota_ramdan, tanggal_pinjaman_ramdan, jumlah_pinjaman_ramdan, bunga_ramdan, total_pinjaman_ramdan, sisa_pinjaman_ramdan, lama_pinjaman_ramdan, status_pinjaman_ramdan) 
          VALUES 
          ('$id_anggota_ramdan', '$tanggal_pinjaman_ramdan', '$jumlah_pinjaman_ramdan', '$bunga_persen', '$total_pinjaman_ramdan', '$sisa_pinjaman_ramdan', '$lama_pinjaman_ramdan', '$status_pinjaman_ramdan')";

$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<script>
        Swal.fire({
            title: 'Berhasil Diajukan!',
            text: 'Data pinjaman baru telah tersimpan dan menunggu persetujuan Admin.',
            icon: 'success',
            showConfirmButton: false,
            timer: 2500
        }).then(() => {
            window.location.href = 'data_pinjaman_ramdan.php';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            title: 'Kesalahan Sistem!',
            text: 'Gagal menyimpan ke database.',
            icon: 'error',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.back();
        });
    </script>";
}

echo "</body></html>";
?>