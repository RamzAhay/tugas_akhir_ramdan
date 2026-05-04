<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// 1. Ambil & Amankan Semua Parameter Filter
$id_a   = mysqli_real_escape_string($koneksi, $_GET['id_anggota'] ?? '');
$jenis  = mysqli_real_escape_string($koneksi, $_GET['jenis_simpanan'] ?? '');
$metode = mysqli_real_escape_string($koneksi, $_GET['metode'] ?? '');
$t1     = mysqli_real_escape_string($koneksi, $_GET['tgl_awal'] ?? '');
$t2     = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');

// 2. Identifikasi Nama Anggota untuk Judul
$nama_anggota = "SEMUA ANGGOTA";
if (!empty($id_a)) {
    $q_info = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$id_a'");
    $d_info = mysqli_fetch_assoc($q_info);
    if ($d_info) $nama_anggota = strtoupper($d_info['nama']);
}

// 3. Bangun Query SQL yang Sinkron dengan riwayat_simpanan.php
$sql = "SELECT s.*, a.nama FROM tb_simpanan_ramdan s 
        INNER JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota 
        WHERE 1=1";

if (!empty($id_a))   $sql .= " AND s.id_anggota = '$id_a'";
if (!empty($jenis))  $sql .= " AND s.jenis_simpanan = '$jenis'";
if (!empty($metode)) $sql .= " AND s.metode_pembayaran = '$metode'";

if (!empty($t1) && !empty($t2)) {
    $sql .= " AND s.tanggal BETWEEN '$t1' AND '$t2'";
} elseif (!empty($t1)) {
    $sql .= " AND s.tanggal >= '$t1'";
} elseif (!empty($t2)) {
    $sql .= " AND s.tanggal <= '$t2'";
}

$sql .= " ORDER BY s.tanggal ASC, s.id_simpanan ASC";
$query = mysqli_query($koneksi, $sql);

// --- KONFIGURASI PDF ---
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header Laporan
$pdf->Cell(190, 7, 'KSP RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 7, 'Laporan Mutasi Simpanan Anggota', 0, 1, 'C');
$pdf->Line(10, 25, 200, 25);
$pdf->Ln(10);

// Info Filter di PDF
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Nama Anggota', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $nama_anggota, 0, 1);
$pdf->Cell(35, 6, 'Kategori', 0, 0);    $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, empty($jenis) ? 'Semua' : $jenis, 0, 1);
$pdf->Cell(35, 6, 'Metode', 0, 0);      $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, empty($metode) ? 'Semua' : $metode, 0, 1);

$periode = "Semua Waktu";
if(!empty($t1) && !empty($t2)) $periode = date('d/m/Y', strtotime($t1)) . " s/d " . date('d/m/Y', strtotime($t2));
$pdf->Cell(35, 6, 'Periode', 0, 0);     $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $periode, 0, 1);
$pdf->Ln(5);

// --- HEADER TABEL ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'NO', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'TANGGAL', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'ANGGOTA', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'METODE', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'DEBIT (SETOR)', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'KREDIT (TARIK)', 1, 1, 'C', true);

// --- ISI DATA ---
$pdf->SetFont('Arial', '', 9);
$no = 1;
$total_debit = 0;
$total_kredit = 0;

if(mysqli_num_rows($query) == 0) {
    $pdf->Cell(190, 10, 'Tidak ada data ditemukan untuk filter ini.', 1, 1, 'C');
}

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(25, 8, date('d/m/Y', strtotime($row['tanggal'])), 1, 0, 'C');
    $pdf->Cell(45, 8, substr(strtoupper($row['nama']), 0, 20), 1, 0, 'L');
    $pdf->Cell(30, 8, $row['metode_pembayaran'], 1, 0, 'C');
    
    if ($row['jumlah'] > 0) {
        $pdf->Cell(40, 8, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(40, 8, '-', 1, 1, 'C');
        $total_debit += $row['jumlah'];
    } else {
        $pdf->Cell(40, 8, '-', 1, 0, 'C');
        $nominal_tarik = abs($row['jumlah']);
        $pdf->Cell(40, 8, 'Rp ' . number_format($nominal_tarik, 0, ',', '.'), 1, 1, 'R');
        $total_kredit += $nominal_tarik;
    }
}

// --- FOOTER TABEL ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(110, 10, 'TOTAL PER PERIODE', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Rp ' . number_format($total_debit, 0, ',', '.'), 1, 0, 'R', true);
$pdf->Cell(40, 10, 'Rp ' . number_format($total_kredit, 0, ',', '.'), 1, 1, 'R', true);

$pdf->SetFillColor(230, 242, 255);
$pdf->Cell(110, 10, 'SALDO BERSIH (NET CHANGE)', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Rp ' . number_format($total_debit - $total_kredit, 0, ',', '.'), 1, 1, 'R', true);

// Tanda Tangan
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Cimahi, ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Bendahara KSP RAMDAN', 0, 1, 'C');
$pdf->Ln(15);
$pdf->Cell(130);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 5, '( ____________________ )', 0, 1, 'C');

$pdf->Output('I', 'Laporan_Simpanan_KSP_Ramdan.pdf');
?>