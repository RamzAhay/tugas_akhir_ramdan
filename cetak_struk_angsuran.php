<?php
include 'koneksi.php';
include 'fpdf/fpdf.php';

// Ambil ID Angsuran dari URL
$id_angsuran = $_GET['id'];

// Query untuk mengambil detail angsuran, nama anggota, dan detail pinjaman
$query = mysqli_query($koneksi, "SELECT a.*, p.jumlah_pinjaman, p.total_pinjaman, p.bunga, ang.nama 
                                FROM tb_angsuran_ramdan a
                                JOIN tb_pinjaman_ramdan p ON a.id_pinjaman = p.id_pinjaman
                                JOIN tb_anggota_ramdan ang ON p.id_anggota = ang.id_anggota
                                WHERE a.id_angsuran = '$id_angsuran'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    die("Data angsuran tidak ditemukan.");
}

// Inisialisasi FPDF (Ukuran kertas kecil cocok untuk struk: 80mm x 150mm)
$pdf = new FPDF('P', 'mm', array(80, 150));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Header Struk
$pdf->Cell(60, 5, 'KOPERASI SIMPAN PINJAM', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(60, 5, 'Jl. Raya No. 123, Indonesia', 0, 1, 'C');
$pdf->Cell(60, 2, '--------------------------------------------------------------', 0, 1, 'C');

// Detail Pembayaran
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60, 5, 'BUKTI ANGSURAN', 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 5, 'No. Kwitansi', 0, 0); $pdf->Cell(5, 5, ':', 0, 0); $pdf->Cell(30, 5, 'STR-'.$data['id_angsuran'], 0, 1);
$pdf->Cell(25, 5, 'Tanggal', 0, 0); $pdf->Cell(5, 5, ':', 0, 0); $pdf->Cell(30, 5, date('d/m/Y', strtotime($data['tanggal_bayar'])), 0, 1);
$pdf->Cell(25, 5, 'Nama', 0, 0); $pdf->Cell(5, 5, ':', 0, 0); $pdf->Cell(30, 5, $data['nama'], 0, 1);

$pdf->Cell(60, 2, '--------------------------------------------------------------', 0, 1, 'C');

// Nominal
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 7, 'BAYAR', 0, 0); $pdf->Cell(5, 7, ':', 0, 0); $pdf->Cell(30, 7, 'Rp '.number_format($data['jumlah_bayar'], 0, ',', '.'), 0, 1);

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(25, 5, 'Sisa Hutang', 0, 0); $pdf->Cell(5, 5, ':', 0, 0); $pdf->Cell(30, 5, 'Rp '.number_format($data['sisa_pinjaman'], 0, ',', '.'), 0, 1);

$pdf->Cell(60, 2, '--------------------------------------------------------------', 0, 1, 'C');

// Footer
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(60, 4, 'Terima kasih atas pembayaran Anda.', 0, 1, 'C');
$pdf->Cell(60, 4, 'Simpan struk ini sebagai bukti sah.', 0, 1, 'C');

$pdf->Output('I', 'Struk_Angsuran_'.$id_angsuran.'.pdf');
?>