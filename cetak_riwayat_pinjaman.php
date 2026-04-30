<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// 1. Tangkap Parameter Filter dari URL
$id_a = mysqli_real_escape_string($koneksi, $_GET['id_anggota'] ?? '');
$t1 = mysqli_real_escape_string($koneksi, $_GET['tgl_awal'] ?? '');
$t2 = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');
$filter_tipe = mysqli_real_escape_string($koneksi, $_GET['filter_tipe'] ?? 'pinjam');

// 2. Ambil Info Anggota jika difilter
$nama_filter = "SEMUA ANGGOTA";
if (!empty($id_a)) {
    $q_info = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$id_a'");
    $d_info = mysqli_fetch_assoc($q_info);
    if ($d_info) {
        $nama_filter = strtoupper($d_info['nama']);
    }
}

// 3. Bangun Kueri SQL dengan Subquery untuk Tanggal Lunas
$sql = "SELECT p.*, a.nama, 
        (SELECT MAX(tanggal_bayar) FROM tb_angsuran_ramdan WHERE id_pinjaman = p.id_pinjaman) as tanggal_lunas
        FROM tb_pinjaman_ramdan p 
        JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
        WHERE p.status_pinjaman != 'Diajukan'";

// Filter Nama Anggota
if (!empty($id_a)) {
    $sql .= " AND p.id_anggota = '$id_a'";
}

// =========================================================================
// LOGIKA FIX: MENENTUKAN KOLOM TANGGAL UNTUK FILTER
// =========================================================================
$kolom_tgl = ($filter_tipe == 'lunas') ? "(SELECT MAX(tanggal_bayar) FROM tb_angsuran_ramdan WHERE id_pinjaman = p.id_pinjaman)" : "p.tanggal_pinjaman";

if (!empty($t1) && !empty($t2)) {
    $sql .= " AND $kolom_tgl BETWEEN '$t1' AND '$t2'";
} elseif (!empty($t1)) {
    $sql .= " AND $kolom_tgl >= '$t1'";
} elseif (!empty($t2)) {
    $sql .= " AND $kolom_tgl <= '$t2'";
}

/**
 * PERUBAHAN URUTAN:
 * Diurutkan berdasarkan Tanggal Pinjam (Terbaru ke Lama)
 */
$sql .= " ORDER BY p.tanggal_pinjaman DESC";
$query = mysqli_query($koneksi, $sql);

// 4. Inisialisasi PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Kop Surat
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(280, 8, 'KOPERASI SIMPAN PINJAM RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(280, 5, 'Laporan Riwayat Pinjaman Selesai & Status Akhir', 0, 1, 'C');

$judul_tipe = ($filter_tipe == 'lunas') ? "BERDASARKAN TANGGAL LUNAS" : "BERDASARKAN TANGGAL PINJAM";
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(280, 10, "RIWAYAT PINJAMAN $judul_tipe", 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(280, 5, 'NAMA ANGGOTA: ' . $nama_filter, 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(280, 1, '', 'T', 1);
$pdf->Ln(5);

if (!empty($t1)) {
    $pdf->SetFont('Arial', 'I', 9);
    $periode_text = date('d/m/Y', strtotime($t1)) . ($t2 ? " s/d " . date('d/m/Y', strtotime($t2)) : " s/d Sekarang");
    $pdf->Cell(280, 5, 'Periode Filter: ' . $periode_text, 0, 1, 'C');
    $pdf->Ln(3);
}

// Header Tabel
$pdf->SetFillColor(33, 41, 53);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'NO', 1, 0, 'C', true);
$pdf->Cell(55, 10, 'NAMA ANGGOTA', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'TGL PINJAM', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'TGL LUNAS', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'JML PINJAMAN', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'TOTAL BAYAR', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'TENOR', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'STATUS', 1, 1, 'C', true);

// Isi Tabel
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$no = 1;

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C');
        $pdf->Cell(55, 8, ' ' . strtoupper($row['nama']), 1, 0, 'L');
        $pdf->Cell(30, 8, date('d/m/Y', strtotime($row['tanggal_pinjaman'])), 1, 0, 'C');
        
        $tgl_lunas = ($row['status_pinjaman'] == 'Lunas' && $row['tanggal_lunas']) ? date('d/m/Y', strtotime($row['tanggal_lunas'])) : '-';
        $pdf->Cell(30, 8, $tgl_lunas, 1, 0, 'C');
        
        $pdf->Cell(40, 8, 'Rp ' . number_format($row['jumlah_pinjaman'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(40, 8, 'Rp ' . number_format($row['total_pinjaman'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(35, 8, $row['lama_pinjaman'] . ' Bulan', 1, 0, 'C');
        
        if($row['status_pinjaman'] == 'Ditolak') $pdf->SetTextColor(200, 0, 0);
        elseif($row['status_pinjaman'] == 'Lunas') $pdf->SetTextColor(0, 150, 0);
        
        $pdf->Cell(40, 8, $row['status_pinjaman'], 1, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
    }
} else {
    $pdf->Cell(280, 15, 'Tidak ada riwayat data yang ditemukan.', 1, 1, 'C');
}

// Tanda Tangan
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(200);
$pdf->Cell(80, 5, 'Cimahi, ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(200);
$pdf->Cell(80, 5, 'Petugas Adm,', 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(200);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 5, strtoupper($_SESSION['nama']), 0, 1, 'C');
$pdf->Cell(200);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(80, 5, 'NIP: ...........................', 'T', 1, 'C');

$pdf->Output('I', 'Riwayat_Pinjaman_' . date('Ymd') . '.pdf');