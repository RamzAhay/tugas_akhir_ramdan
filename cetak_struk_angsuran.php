<?php
include 'auth.php';
include 'koneksi.php';

/** * Menggunakan folder 'FPDF' sesuai struktur folder kamu.
 */
require('FPDF/fpdf.php'); 

// Menangkap ID Angsuran dari URL
if (!isset($_GET['id'])) {
    die("ID Angsuran tidak ditemukan.");
}

$id_angsuran = mysqli_real_escape_string($koneksi, $_GET['id']);

/**
 * QUERY UNTUK STRUK:
 * Mengambil detail angsuran, sisa pinjaman terbaru, dan nama anggota.
 */
$sql = "SELECT a.*, p.sisa_pinjaman, p.id_pinjaman, ang.nama 
        FROM tb_angsuran_ramdan a 
        JOIN tb_pinjaman_ramdan p ON a.id_pinjaman = p.id_pinjaman 
        JOIN tb_anggota_ramdan ang ON p.id_anggota = ang.id_anggota 
        WHERE a.id_angsuran = '$id_angsuran'";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data angsuran tidak ditemukan.");
}

/**
 * LOGIKA HITUNG ANGSURAN KE-BERAPA:
 * Menghitung jumlah record angsuran untuk pinjaman ini yang ID-nya <= ID saat ini.
 */
$id_p = $data['id_pinjaman'];
$q_hitung = mysqli_query($koneksi, "SELECT COUNT(*) as ke FROM tb_angsuran_ramdan WHERE id_pinjaman = '$id_p' AND id_angsuran <= '$id_angsuran'");
$d_hitung = mysqli_fetch_assoc($q_hitung);
$angsuran_ke = $d_hitung['ke'];

// Persiapan Data
$no_struk  = "STR-" . $data['id_angsuran'];
$tgl_bayar = date('d/m/Y H:i', strtotime($data['tanggal_bayar']));
$nama      = strtoupper($data['nama']);
$nominal   = rupiah($data['jumlah_bayar']);
$sisa      = rupiah($data['sisa_pinjaman']);
$petugas   = strtoupper($_SESSION['nama']);

/**
 * PROSES GENERATE PDF
 * Ukuran: 80mm (lebar thermal) x 120mm (tinggi)
 */
$pdf = new FPDF('P', 'mm', array(80, 120));
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// --- HEADER KOPERASI ---
$pdf->SetFont('Courier', 'B', 14);
$pdf->Cell(70, 8, 'KSP RAMDAN', 0, 1, 'C');
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(70, 4, 'Jl. Raya No. 123, Indonesia', 0, 1, 'C');
$pdf->Cell(70, 4, 'Bukti Pembayaran Sah', 0, 1, 'C');
$pdf->Ln(2);

// --- GARIS PEMISAH ---
$pdf->SetFont('Courier', '', 10);
$pdf->Cell(70, 4, '===========================', 0, 1, 'C');
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(70, 6, 'STRUK ANGSURAN', 0, 1, 'C');
$pdf->SetFont('Courier', '', 10);
$pdf->Cell(70, 4, '===========================', 0, 1, 'C');
$pdf->Ln(3);

// --- INFO TRANSAKSI ---
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(25, 5, 'No Struk', 0, 0);
$pdf->Cell(45, 5, ': ' . $no_struk, 0, 1);

$pdf->Cell(25, 5, 'Tanggal', 0, 0);
$pdf->Cell(45, 5, ': ' . $tgl_bayar, 0, 1);

$pdf->Cell(25, 5, 'Anggota', 0, 0);
$pdf->Cell(45, 5, ': ' . $nama, 0, 1);

// Menambahkan keterangan angsuran ke-berapa
$pdf->SetFont('Courier', 'B', 9);
$pdf->Cell(25, 5, 'Keterangan', 0, 0);
$pdf->Cell(45, 5, ': ANGSURAN KE-' . $angsuran_ke, 0, 1);
$pdf->Ln(2);

$pdf->Cell(70, 0, '', 'T', 1); // Garis horizontal
$pdf->Ln(3);

// --- RINCIAN BIAYA ---
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(30, 7, 'BAYAR', 0, 0);
$pdf->Cell(40, 7, $nominal, 0, 1, 'R');

// Warna biru tema web kamu (RGB: 13, 110, 253)
$pdf->SetTextColor(13, 110, 253); 
$pdf->Cell(30, 7, 'SISA HUTANG', 0, 0);
$pdf->Cell(40, 7, $sisa, 0, 1, 'R');

// Kembalikan warna ke hitam
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(3);
$pdf->Cell(70, 0, '', 'T', 1);
$pdf->Ln(5);

// --- FOOTER ---
$pdf->SetFont('Courier', '', 8);
$pdf->Cell(70, 4, 'Terima kasih atas pembayarannya.', 0, 1, 'C');
$pdf->Cell(70, 4, 'Simpan struk ini baik-baik.', 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(70, 4, 'Kasir: ' . $petugas, 0, 1, 'C');
$pdf->SetFont('Courier', 'I', 7);
$pdf->Cell(70, 4, 'Dicetak: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

// Output: Langsung tampilkan di browser
$pdf->Output('I', 'Struk_' . $no_struk . '.pdf');
?>