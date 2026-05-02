<?php
/**
 * FILE HELPER UNTUK EFISIENSI KODE
 * Fungsi-fungsi di sini digunakan untuk menyeragamkan tampilan data.
 */

// 1. Fungsi Format Rupiah Standard
function rupiah($angka) {
    if ($angka === null || $angka === '') return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

// 2. Fungsi Format Tanggal Indonesia (Lebih rapi untuk Sidang TA)
function tgl_indo($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') return "-";
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    // Contoh: 15 Mei 2024
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// 3. Fungsi Pendek untuk Tanggal (dd/mm/yy)
function tgl_kecil($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') return "-";
    return date('d/m/y', strtotime($tanggal));
}

// 4. Fungsi Alert & Redirect dengan SweetAlert2
function alert_redirect($pesan, $lokasi) {
    echo "<script>
            Swal.fire({
                title: 'Informasi',
                text: '$pesan',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '$lokasi';
            });
          </script>";
    exit();
}
?>