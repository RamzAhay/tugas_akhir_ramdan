<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

if (isset($_POST['submit'])) {
    // 1. Menangkap data dari form (tambah_angsuran_ramdan.php)
    $id_pinjaman_ramdan = mysqli_real_escape_string($koneksi, $_POST['id_pinjaman_ramdan']);
    $tanggal_bayar_ramdan = mysqli_real_escape_string($koneksi, $_POST['tanggal_bayar_ramdan']);
    $metode_pembayaran_ramdan = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran_ramdan']);
    
    // Pastikan nominal bayar adalah angka murni (int)
    $jumlah_bayar_ramdan = (int)$_POST['jumlah_bayar_ramdan'];

    // Menampilkan header agar SweetAlert2 bisa muncul dengan rapi
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body{font-family:'Poppins', sans-serif; background-color:#f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}</style></head><body>";

    // 2. Cek sisa hutang saat ini di database sebelum diproses
    $query_cek = mysqli_query($koneksi, "SELECT sisa_pinjaman_ramdan FROM tb_pinjaman_ramdan WHERE id_pinjaman_ramdan = '$id_pinjaman_ramdan'");
    $data_pinjaman = mysqli_fetch_assoc($query_cek);
    
    if (!$data_pinjaman) {
        echo "<script>alert('Data pinjaman tidak ditemukan!'); window.location='data_angsuran_ramdan.php';</script>";
        exit();
    }

    $sisa_sebelumnya = $data_pinjaman['sisa_pinjaman_ramdan'];

    // 3. Validasi: Jangan biarkan bayar lebih dari sisa hutang
    if ($jumlah_bayar_ramdan > $sisa_sebelumnya) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Ditolak',
                text: 'Nominal bayar (Rp " . number_format($jumlah_bayar_ramdan,0,',','.') . ") melebihi sisa hutang (Rp " . number_format($sisa_sebelumnya,0,',','.') . ")',
                confirmButtonColor: '#0d6efd'
            }).then(() => { window.history.back(); });
        </script>";
        exit();
    }

    // 4. Masukkan data ke tabel angsuran
    $sql_ins = "INSERT INTO tb_angsuran_ramdan (id_pinjaman_ramdan, tanggal_bayar_ramdan, jumlah_bayar_ramdan, metode_pembayaran_ramdan) 
                VALUES ('$id_pinjaman_ramdan', '$tanggal_bayar_ramdan', '$jumlah_bayar_ramdan', '$metode_pembayaran_ramdan')";
    
    $query_ins = mysqli_query($koneksi, $sql_ins);

    if ($query_ins) {
        // 5. Update data di tabel pinjaman (Hitung sisa hutang baru)
        $sisa_baru = $sisa_sebelumnya - $jumlah_bayar_ramdan;
        
        // Jika sisa baru adalah 0, maka status berubah jadi 'Lunas'
        $status_baru = ($sisa_baru <= 0) ? 'Lunas' : 'Disetujui';
        
        $sql_upd = "UPDATE tb_pinjaman_ramdan SET 
                    sisa_pinjaman_ramdan = '$sisa_baru', 
                    status_pinjaman_ramdan = '$status_baru' 
                    WHERE id_pinjaman_ramdan = '$id_pinjaman_ramdan'";
        
        mysqli_query($koneksi, $sql_upd);

        // Berikan notifikasi sukses
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Angsuran berhasil dicatat. Sisa hutang: Rp " . number_format($sisa_baru, 0, ',', '.') . "',
                showConfirmButton: false,
                timer: 2500
            }).then(() => {
                window.location.href = 'riwayat_angsuran_ramdan.php?id=$id_pinjaman_ramdan';
            });
        </script>";
    } else {
        // Jika terjadi error pada query database
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error Database',
                text: '" . mysqli_error($koneksi) . "',
                confirmButtonColor: '#d33'
            }).then(() => { window.history.back(); });
        </script>";
    }
    echo "</body></html>";
} else {
    // Jika file diakses langsung tanpa lewat form
    header("Location: data_angsuran_ramdan.php");
    exit();
}
?>