<?php
include 'auth.php';
include 'koneksi.php';
require('fpdf/fpdf.php'); 

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'KOPERASI SIMPAN PINJAM RAMDAN',0,1,'C'); 
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Laporan Data Simpanan Anggota',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'Jl. Contoh Alamat No. 123, Kota, Provinsi | Telp: 0812-3456-7890',0,1,'C');
$pdf->Line(10, 35, 200, 35); 
$pdf->Ln(10); 

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(60,10,'Nama Anggota',1,0,'C');
$pdf->Cell(40,10,'Jenis Simpanan',1,0,'C');
$pdf->Cell(40,10,'Jumlah (Rp)',1,0,'C');
$pdf->Cell(40,10,'Tanggal',1,1,'C');

$pdf->SetFont('Arial','',10);
$query = mysqli_query($koneksi, "SELECT s.*, a.nama FROM tb_simpanan_ramdan s JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota ORDER BY s.id_simpanan ASC");
$no = 1;
while($data = mysqli_fetch_assoc($query)) { 
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(60,10,$data['nama'],1,0,'L');
    $pdf->Cell(40,10,$data['jenis_simpanan'],1,0,'C');
    $pdf->Cell(40,10,'Rp '.number_format($data['jumlah'],0,',','.'),1,0,'R');
    $pdf->Cell(40,10,date('d-m-Y', strtotime($data['tanggal'])),1,1,'C');
}

$pdf->Output('I', 'Laporan_Simpanan.pdf');
?>