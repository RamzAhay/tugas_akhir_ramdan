<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// Tangkap ID Pinjaman
if (!isset($_GET['id'])) {
    die("ID Pinjaman tidak ditemukan.");
}

$id_p = mysqli_real_escape_string($koneksi, $_GET['id']);

// 1. Ambil Info Pinjaman & Anggota
$q_pinjam = mysqli_query($koneksi, "SELECT p.*, a.nama FROM tb_pinjaman_ramdan p 
                                    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                    WHERE p.id_pinjaman = '$id_p'");
$d = mysqli_fetch_assoc($q_pinjam);

if (!$d) {
    die("Data Pinjaman tidak ditemukan.");
}

// Perhitungan Ringkasan
$total_hutang = $d['total_pinjaman'];
$sisa_hutang = $d['sisa_pinjaman'];
$sudah_dibayar = $total_hutang - $sisa_hutang;

// 2. Inisialisasi PDF (Ukuran A4 Potrait)
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// --- KOP SURAT ---
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 7, 'KOPERASI SIMPAN PINJAM RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 5, 'Sistem Informasi Pengelolaan Keuangan Koperasi Terpadu', 0, 1, 'C');
$pdf->Cell(190, 5, 'Jl. Permana No. 67, Kota Cimahi | Telp: 0812-3456-7890', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(190, 1, '', 'T', 1); // Garis tebal
$pdf->Cell(190, 1, '', 'B', 1); // Garis tipis
$pdf->Ln(5);

// --- JUDUL LAPORAN ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'LAPORAN RIWAYAT PEMBAYARAN ANGSURAN', 0, 1, 'C');
$pdf->Ln(2);

// --- INFO DETAIL PINJAMAN ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Nama Anggota', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 6, strtoupper($d['nama']), 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Status', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', 'B', 10);

// Ganti warna teks jika sudah lunas
if($d['status_pinjaman'] == 'Lunas') {
    $pdf->SetTextColor(0, 150, 0);
} elseif ($d['status_pinjaman'] == 'Ditolak') {
    $pdf->SetTextColor(255, 0, 0);
}
$pdf->Cell(30, 6, strtoupper($d['status_pinjaman']), 0, 1);
$pdf->SetTextColor(0, 0, 0); // Kembalikan ke hitam

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Tgl Pinjam', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 6, date('d M Y', strtotime($d['tanggal_pinjaman'])), 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Tenor', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, $d['lama_pinjaman'] . ' Bulan', 0, 1);
$pdf->Ln(5);

// --- KOTAK RINGKASAN KEUANGAN ---
$pdf->SetFillColor(240, 248, 255); // Warna Biru Sangat Muda
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(63, 8, 'TOTAL PINJAMAN', 1, 0, 'C', true);
$pdf->Cell(64, 8, 'SUDAH DIBAYAR', 1, 0, 'C', true);
$pdf->Cell(63, 8, 'SISA HUTANG', 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(63, 10, rupiah($total_hutang), 1, 0, 'C');
$pdf->SetTextColor(0, 128, 0); // Hijau
$pdf->Cell(64, 10, rupiah($sudah_dibayar), 1, 0, 'C');
$pdf->SetTextColor(220, 53, 69); // Merah
$pdf->Cell(63, 10, rupiah($sisa_hutang), 1, 1, 'C');
$pdf->SetTextColor(0, 0, 0); // Kembalikan Hitam
$pdf->Ln(5);

// --- HEADER TABEL RIWAYAT ---
$pdf->SetFillColor(51, 65, 85); // Slate Dark
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 10, 'NO', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'TANGGAL BAYAR', 1, 0, 'C', true);
$pdf->Cell(70, 10, 'KETERANGAN', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'NOMINAL BAYAR', 1, 1, 'C', true);

// --- ISI TABEL RIWAYAT ---
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);

// Urutkan angsuran dari yang paling awal dibayar (ASC)
$q_list = mysqli_query($koneksi, "SELECT * FROM tb_angsuran_ramdan WHERE id_pinjaman = '$id_p' ORDER BY tanggal_bayar ASC, id_angsuran ASC");

if (mysqli_num_rows($q_list) > 0) {
    $no = 1;
    while ($r = mysqli_fetch_assoc($q_list)) {
        $pdf->Cell(15, 8, $no, 1, 0, 'C');
        $pdf->Cell(45, 8, date('d/m/Y', strtotime($r['tanggal_bayar'])), 1, 0, 'C');
        $pdf->Cell(70, 8, ' Angsuran Ke-' . $no, 1, 0, 'L');
        $pdf->Cell(60, 8, rupiah($r['jumlah_bayar']), 1, 1, 'R');
        $no++;
    }
} else {
    $pdf->Cell(190, 15, 'Belum ada riwayat pembayaran angsuran untuk pinjaman ini.', 1, 1, 'C');
}

// --- TANDA TANGAN ---
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Cimahi, ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Petugas Koperasi,', 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(130);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 5, strtoupper($_SESSION['nama']), 0, 1, 'C');
$pdf->Cell(130);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, 'NIP. ...........................', 'T', 1, 'C');

// Output PDF
$pdf->Output('I', 'Riwayat_Angsuran_' . str_replace(' ', '_', $d['nama']) . '.pdf');
?>