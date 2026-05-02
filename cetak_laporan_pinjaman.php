<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// 1. Tangkap Parameter Filter dari URL
$id_a = mysqli_real_escape_string($koneksi, $_GET['id_anggota'] ?? '');
$status_f = mysqli_real_escape_string($koneksi, $_GET['status'] ?? '');
$tgl1 = mysqli_real_escape_string($koneksi, $_GET['tgl_awal'] ?? '');
$tgl2 = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');

// 2. Tentukan Judul Berdasarkan Filter
$sub_judul = "SEMUA PINJAMAN AKTIF";
if ($status_f == 'Diajukan') {
    $sub_judul = "DAFTAR PENGAJUAN PINJAMAN (PENDING)";
} elseif ($status_f == 'Disetujui') {
    $sub_judul = "DAFTAR PINJAMAN BERJALAN (AKTIF)";
}

// 3. Bangun Kueri SQL (Harus Sesuai dengan data_pinjaman.php)
$sql = "SELECT p.*, a.nama 
        FROM tb_pinjaman_ramdan p 
        JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
        WHERE p.status_pinjaman NOT IN ('Lunas', 'Ditolak')";

// Filter Anggota
if (!empty($id_a)) {
    $sql .= " AND p.id_anggota = '$id_a'";
}

// Filter Status (Diajukan / Disetujui)
if (!empty($status_f)) {
    $sql .= " AND p.status_pinjaman = '$status_f'";
}

// Filter Tanggal
if (!empty($tgl1) && !empty($tgl2)) {
    $sql .= " AND p.tanggal_pinjaman BETWEEN '$tgl1' AND '$tgl2'";
} elseif (!empty($tgl1)) {
    $sql .= " AND p.tanggal_pinjaman >= '$tgl1'";
} elseif (!empty($tgl2)) {
    $sql .= " AND p.tanggal_pinjaman <= '$tgl2'";
}

$sql .= " ORDER BY p.id_pinjaman DESC";
$query = mysqli_query($koneksi, $sql);

// 4. Proses Desain PDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape agar kolom muat banyak
$pdf->AddPage();

// Kop Surat
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(280, 8, 'KOPERASI SIMPAN PINJAM RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(280, 5, 'Laporan Pelacakan Pinjaman Anggota', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(280, 7, $sub_judul, 0, 1, 'C');
$pdf->Ln(5);

// Info Periode jika ada
if (!empty($tgl1)) {
    $pdf->SetFont('Arial', 'I', 9);
    $periode = date('d/m/Y', strtotime($tgl1)) . ($tgl2 ? " s/d " . date('d/m/Y', strtotime($tgl2)) : " s/d Sekarang");
    $pdf->Cell(280, 5, 'Periode: ' . $periode, 0, 1, 'C');
    $pdf->Ln(3);
}

// Header Tabel
$pdf->SetFillColor(51, 65, 85); // Warna Slate
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'NO', 1, 0, 'C', true);
$pdf->Cell(65, 10, 'NAMA ANGGOTA', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'TGL PINJAM', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'JUMLAH PINJAMAN', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'SISA HUTANG', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'TENOR', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'STATUS', 1, 1, 'C', true);

// Isi Tabel
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$no = 1;
$total_piutang = 0;

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C');
        $pdf->Cell(65, 8, ' ' . strtoupper($row['nama']), 1, 0, 'L');
        $pdf->Cell(35, 8, date('d/m/Y', strtotime($row['tanggal_pinjaman'])), 1, 0, 'C');
        $pdf->Cell(45, 8, rupiah($row['jumlah_pinjaman']), 1, 0, 'R');
        $pdf->Cell(45, 8, rupiah($row['sisa_pinjaman']), 1, 0, 'R');
        $pdf->Cell(35, 8, $row['lama_pinjaman'] . ' Bulan', 1, 0, 'C');
        $pdf->Cell(45, 8, $row['status_pinjaman'], 1, 1, 'C');
        
        $total_piutang += $row['sisa_pinjaman'];
    }

    // Footer Tabel (Total)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(241, 245, 249);
    $pdf->Cell(155, 10, 'TOTAL PIUTANG AKTIF (SALDO DI LUAR)', 1, 0, 'R', true);
    $pdf->Cell(45, 10, rupiah($total_piutang), 1, 0, 'R', true);
    $pdf->Cell(80, 10, '', 1, 1, 'C', true);
} else {
    $pdf->Cell(280, 15, 'Tidak ada data pinjaman yang ditemukan.', 1, 1, 'C');
}

// Tanda Tangan
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(200);
$pdf->Cell(80, 5, 'Cimahi, ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(200);
$pdf->Cell(80, 5, 'Dicetak Oleh:', 0, 1, 'C');
$pdf->Ln(15);
$pdf->Cell(200);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 5, strtoupper($_SESSION['nama']), 0, 1, 'C');
$pdf->Cell(200);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(80, 5, 'Role: ' . $_SESSION['role'], 'T', 1, 'C');

$pdf->Output('I', 'Laporan_Pinjaman_Aktif.pdf');