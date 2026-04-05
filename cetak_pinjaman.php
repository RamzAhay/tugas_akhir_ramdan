<?php
include 'auth.php';
include 'koneksi.php';

// Panggil library FPDF
require('fpdf/fpdf.php'); 

// Bikin objek PDF baru (L = Landscape, mm = milimeter, A4 = ukuran kertas)
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();

// --- HEADER LAPORAN ---
$pdf->SetFont('Arial','B',16);
// Parameter Cell: (lebar, tinggi, teks, border, pindah_baris, rata_tengah)
$pdf->Cell(277,10,'KOPERASI SIMPAN PINJAM RAMDAN',0,1,'C'); 

$pdf->SetFont('Arial','B',12);
$pdf->Cell(277,10,'Laporan Data Pinjaman Anggota',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(277,5,'Jl. Contoh Alamat No. 123, Kota, Provinsi | Telp: 0812-3456-7890',0,1,'C');

// Garis bawah untuk kop surat (X1, Y1, X2, Y2)
$pdf->Line(10, 35, 287, 35); 
$pdf->Ln(10); // Spasi baris (Enter)

// --- HEADER TABEL ---
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(50,10,'Nama Anggota',1,0,'C');
$pdf->Cell(35,10,'Tgl Pinjam',1,0,'C');
$pdf->Cell(45,10,'Jml Pinjaman',1,0,'C');
$pdf->Cell(20,10,'Bunga',1,0,'C');
$pdf->Cell(45,10,'Total Hutang',1,0,'C');
$pdf->Cell(35,10,'Status',1,1,'C'); // 1 di akhir berarti pindah baris

// --- ISI TABEL ---
$pdf->SetFont('Arial','',10);

$query = mysqli_query($koneksi, "
    SELECT p.*, a.nama 
    FROM tb_pinjaman_ramdan p
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota
    ORDER BY p.id_pinjaman ASC
");

$no = 1;
while($data = mysqli_fetch_assoc($query)) { 
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(50,10,$data['nama'],1,0,'L');
    $pdf->Cell(35,10,date('d-m-Y', strtotime($data['tanggal_pinjaman'])),1,0,'C');
    $pdf->Cell(45,10,'Rp '.number_format($data['jumlah_pinjaman'],0,',','.'),1,0,'R');
    $pdf->Cell(20,10,$data['bunga'].'%',1,0,'C');
    $pdf->Cell(45,10,'Rp '.number_format($data['total_pinjaman'],0,',','.'),1,0,'R');
    $pdf->Cell(35,10,$data['status_pinjaman'],1,1,'C');
}

// --- TANDA TANGAN ---
$pdf->Ln(15); // Kasih jarak
$pdf->SetFont('Arial','',10);
$pdf->Cell(230); // Geser kotak ke kanan sejauh 230mm
$pdf->Cell(47,5,'Mengetahui,',0,1,'C');
$pdf->Ln(20); // Jarak untuk tanda tangan
$pdf->Cell(230); 
$pdf->SetFont('Arial','B',10);
$pdf->Cell(47,5,'( Ketua Koperasi )',0,1,'C');

// --- OUTPUT PDF ---
// 'I' artinya "Inline" (tampilkan di browser). Kalau mau langsung download otomatis, ganti jadi 'D'
$pdf->Output('I', 'Laporan_Pinjaman_Ramdan.pdf');
?>