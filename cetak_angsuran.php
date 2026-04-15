<?php
include 'auth.php';
include 'koneksi.php';
require('fpdf/fpdf.php'); 

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'KOPERASI SIMPAN PINJAM RAMDAN',0,1,'C'); 
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Laporan Pembayaran Angsuran',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'Jl. Permana No. 67, Kota Cimahi, Provinsi Jawa Barat | Telp: 0812-3456-7890',0,1,'C');
$pdf->Line(10, 35, 200, 35); 
$pdf->Ln(10); 

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(60,10,'Nama Anggota',1,0,'C');
$pdf->Cell(40,10,'Tanggal Bayar',1,0,'C');
$pdf->Cell(40,10,'Jumlah Bayar',1,0,'C');
$pdf->Cell(40,10,'Sisa Hutang',1,1,'C');

$pdf->SetFont('Arial','',10);
$query = mysqli_query($koneksi, "SELECT ans.*, p.total_pinjaman, a.nama FROM tb_angsuran_ramdan ans JOIN tb_pinjaman_ramdan p ON ans.id_pinjaman = p.id_pinjaman JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota ORDER BY ans.id_angsuran ASC");
$no = 1;
while($data = mysqli_fetch_assoc($query)) { 
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(60,10,$data['nama'],1,0,'L');
    $pdf->Cell(40,10,date('d-m-Y', strtotime($data['tanggal_bayar'])),1,0,'C');
    $pdf->Cell(40,10,'Rp '.number_format($data['jumlah_bayar'],0,',','.'),1,0,'R');
    $pdf->Cell(40,10,'Rp '.number_format($data['sisa_pinjaman'],0,',','.'),1,1,'R');
}

$pdf->Output('I', 'Laporan_Angsuran.pdf');
?>