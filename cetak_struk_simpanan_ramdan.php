<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

/** * Menggunakan library FPDF yang sudah ada di folder proyek kamu.
 */
require('FPDF/fpdf.php'); 

// Menangkap ID Simpanan dari URL
if (!isset($_GET['id'])) {
    die("ID Transaksi tidak ditemukan.");
}

$id_simpanan_ramdan = mysqli_real_escape_string($koneksi, $_GET['id']);

/**
 * QUERY UNTUK STRUK:
 * Mengambil detail simpanan dan nama_ramdan anggota.
 */
$sql = "SELECT s.*, a.nama_ramdan 
        FROM tb_simpanan_ramdan s 
        JOIN tb_anggota_ramdan a ON s.id_anggota_ramdan = a.id_anggota_ramdan 
        WHERE s.id_simpanan_ramdan = '$id_simpanan_ramdan'";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data transaksi tidak ditemukan.");
}

// Persiapan Data
$no_reff   = "SIM-" . $data['id_simpanan_ramdan'];
$tgl_trans = date('d/m/Y H:i', strtotime($data['tanggal_ramdan']));
$nama_ramdan      = strtoupper($data['nama_ramdan']);
$jenis     = $data['jenis_simpanan_ramdan'];
$nominal   = abs($data['jumlah_ramdan']); // Menggunakan abs() agar nilai negatif pada penarikan tampil positif di struk
$is_tarik  = ($data['jumlah_ramdan'] < 0);
$petugas   = strtoupper($_SESSION['nama_ramdan']);
$metode    = (isset($data['metode_pembayaran_ramdan']) && $data['metode_pembayaran_ramdan'] != '') ? strtoupper($data['metode_pembayaran_ramdan']) : 'TUNAI';

/**
 * PROSES GENERATE PDF
 * Ukuran: 80mm (lebar thermal) x 110mm (tinggi) - Sedikit dipanjangkan
 */
$pdf = new fpdf('P', 'mm', array(80, 110));
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// --- HEADER KOPERASI ---
$pdf->SetFont('Courier', 'B', 14);
$pdf->Cell(70, 8, 'KSP RAMDAN', 0, 1, 'C');
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(70, 4, 'BUKTI TRANSAKSI SIMPANAN', 0, 1, 'C');
$pdf->Ln(2);

// --- GARIS PEMISAH ---
$pdf->SetFont('Courier', '', 10);
$pdf->Cell(70, 4, '===========================', 0, 1, 'C');
$pdf->Ln(2);

// --- INFO TRANSAKSI ---
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(25, 5, 'No Reff', 0, 0);
$pdf->Cell(45, 5, ': ' . $no_reff, 0, 1);

$pdf->Cell(25, 5, 'Tanggal', 0, 0);
$pdf->Cell(45, 5, ': ' . $tgl_trans, 0, 1);

$pdf->Cell(25, 5, 'Anggota', 0, 0);
$pdf->Cell(45, 5, ': ' . $nama_ramdan, 0, 1);

$pdf->Cell(25, 5, 'Tipe', 0, 0);
$pdf->Cell(45, 5, ': ' . ($is_tarik ? 'PENARIKAN' : 'SETORAN ' . strtoupper($jenis)), 0, 1);

// BARIS BARU: Menampilkan Metode Pembayaran
$pdf->Cell(25, 5, 'Metode', 0, 0);
$pdf->Cell(45, 5, ': ' . $metode, 0, 1);

$pdf->Ln(2);
$pdf->Cell(70, 0, '', 'T', 1); // Garis horizontal
$pdf->Ln(3);

// --- NOMINAL ---
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(30, 8, 'TOTAL', 0, 0);
$pdf->Cell(40, 8, 'Rp ' . number_format($nominal, 0, ',', '.'), 0, 1, 'R');

$pdf->Ln(3);
$pdf->Cell(70, 0, '', 'T', 1);
$pdf->Ln(5);

// --- FOOTER ---
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(70, 4, 'Simpan struk ini sebagai', 0, 1, 'C');
$pdf->Cell(70, 4, 'bukti transaksi yang sah.', 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(70, 4, 'Kasir: ' . $petugas, 0, 1, 'C');

// Output
$pdf->Output('I', 'Struk_' . $no_reff . '.pdf');
?>