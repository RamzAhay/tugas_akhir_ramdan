<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// Menangkap Filter dari URL
$id_anggota = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';

// Query SQL dengan filter yang sama
$sql = "SELECT p.*, a.nama 
        FROM tb_pinjaman_ramdan p 
        JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
        WHERE p.status_pinjaman NOT IN ('Lunas', 'Ditolak')";

if ($id_anggota != '') $sql .= " AND p.id_anggota = '$id_anggota'";
if ($tgl_awal != '' && $tgl_akhir != '') {
    $sql .= " AND p.tanggal_pinjaman BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}
$sql .= " ORDER BY p.id_pinjaman DESC";
$query = mysqli_query($koneksi, $sql);

// Inisialisasi PDF (Landscape untuk tabel lebar)
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(280, 10, 'KSP RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(280, 7, 'LAPORAN PELACAKAN PINJAMAN AKTIF', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(280, 7, 'Dicetak pada: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'NO', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'NAMA ANGGOTA', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'TGL PINJAM', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'JML PINJAMAN', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'BUNGA (%)', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'TENOR', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'SISA HUTANG', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'STATUS', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 10);
$no = 1;
$total_sisa = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(60, 8, strtoupper($row['nama']), 1, 0, 'L');
    $pdf->Cell(35, 8, date('d/m/Y', strtotime($row['tanggal_pinjaman'])), 1, 0, 'C');
    $pdf->Cell(45, 8, 'Rp ' . number_format($row['jumlah_pinjaman'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(30, 8, $row['bunga'] . '%', 1, 0, 'C');
    $pdf->Cell(30, 8, $row['lama_pinjaman'] . ' Bln', 1, 0, 'C');
    $pdf->Cell(45, 8, 'Rp ' . number_format($row['sisa_pinjaman'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(25, 8, $row['status_pinjaman'], 1, 1, 'C');
    $total_sisa += $row['sisa_pinjaman'];
}

// Summary
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(210, 10, 'TOTAL DANA DI LUAR (OUTSTANDING)', 1, 0, 'R', true);
$pdf->Cell(70, 10, 'Rp ' . number_format($total_sisa, 0, ',', '.'), 1, 1, 'R', true);

$pdf->Output('I', 'Laporan_Pinjaman_Aktif.pdf');
?>