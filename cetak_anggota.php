<?php
include 'auth.php';
include 'koneksi.php';
require('fpdf/fpdf.php'); 

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'KOPERASI SIMPAN PINJAM RAMDAN',0,1,'C'); 
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Laporan Data Anggota',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'Jl. Contoh Alamat No. 123, Kota, Provinsi | Telp: 0812-3456-7890',0,1,'C');
$pdf->Line(10, 35, 200, 35); 
$pdf->Ln(10); 

// Header Tabel
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(60,10,'Nama Lengkap',1,0,'C');
$pdf->Cell(80,10,'Alamat',1,0,'C');
$pdf->Cell(40,10,'No HP',1,1,'C');

// Isi Tabel
$pdf->SetFont('Arial','',10);
$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota ASC");
$no = 1;
while($data = mysqli_fetch_assoc($query)) { 
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(60,10,$data['nama'],1,0,'L');
    $pdf->Cell(80,10,$data['alamat'],1,0,'L');
    $pdf->Cell(40,10,$data['no_hp'],1,1,'C');
}

$pdf->Output('I', 'Laporan_Anggota.pdf');
?>