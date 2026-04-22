<?php
// Memanggil koneksi dan library FPDF
include 'koneksi.php';
include 'fpdf/fpdf.php';

// Menangkap ID Angsuran dari URL
$id_angsuran = $_GET['id'];

// Query untuk mengambil data angsuran beserta nama anggota (JOIN tabel)
$query = mysqli_query($koneksi, "
    SELECT a.*, p.id_anggota, an.nama 
    FROM tb_angsuran_ramdan a 
    JOIN tb_pinjaman_ramdan p ON a.id_pinjaman = p.id_pinjaman 
    JOIN tb_anggota_ramdan an ON p.id_anggota = an.id_anggota 
    WHERE a.id_angsuran = '$id_angsuran'
");
$data = mysqli_fetch_assoc($query);

// Membuat PDF baru dengan ukuran kertas A5 (Portrait)
$pdf = new FPDF('P', 'mm', 'A5');
$pdf->AddPage();

// ---------------- HEADER STRUK ----------------
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, 'KOPERASI SIMPAN PINJAM RAMDAN', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Jl. Raya Koperasi No. 123, Kota Koding', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: 0812-3456-7890', 0, 1, 'C');

// Garis Pemisah
$pdf->Ln(2);
$pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'BUKTI PEMBAYARAN ANGSURAN', 0, 1, 'C');
$pdf->Ln(5);

// ---------------- ISI STRUK ----------------
$pdf->SetFont('Arial', '', 11);

// Menampilkan Data
$pdf->Cell(40, 8, 'No. Transaksi', 0, 0);
$pdf->Cell(5, 8, ':', 0, 0);
$pdf->Cell(0, 8, 'TRX-ANG-'.$data['id_angsuran'], 0, 1);

$pdf->Cell(40, 8, 'Tanggal Bayar', 0, 0);
$pdf->Cell(5, 8, ':', 0, 0);
$pdf->Cell(0, 8, date('d-m-Y', strtotime($data['tanggal_bayar'])), 0, 1); // Ganti pakai tanggal_bayar

$pdf->Cell(40, 8, 'Nama Anggota', 0, 0);
$pdf->Cell(5, 8, ':', 0, 0);
$pdf->Cell(0, 8, $data['nama'], 0, 1);

// BAGIAN ANGSURAN KE SUDAH DIHAPUS DARI SINI

$pdf->Cell(40, 8, 'Jumlah Bayar', 0, 0);
$pdf->Cell(5, 8, ':', 0, 0);
$pdf->SetFont('Arial', 'B', 11); // Angka dibikin tebal
$pdf->Cell(0, 8, 'Rp ' . number_format($data['jumlah_bayar'], 0, ',', '.'), 0, 1);

// Garis Pemisah Bawah
$pdf->Ln(5);
$pdf->SetLineWidth(0.5); // Garis agak tebal
$pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());
$pdf->SetLineWidth(0.2); // Kembalikan ke normal
$pdf->Ln(8);

// ---------------- FOOTER STRUK ----------------
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 5, 'Terima kasih telah melakukan pembayaran tepat waktu.', 0, 1, 'C');
$pdf->Cell(0, 5, 'Struk ini adalah bukti pembayaran yang sah.', 0, 1, 'C');

$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80); // Geser ke kanan
$pdf->Cell(40, 5, 'Petugas Koperasi', 0, 1, 'C');
$pdf->Ln(15); // Jarak untuk tanda tangan
$pdf->Cell(80);
$pdf->Cell(40, 5, '( ............................... )', 0, 1, 'C');

// Menampilkan PDF di browser
$pdf->Output('I', 'Struk_Angsuran_'.$data['id_angsuran'].'.pdf');
?>