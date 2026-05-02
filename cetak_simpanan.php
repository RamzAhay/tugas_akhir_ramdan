<?php
include 'auth.php';
include 'koneksi.php';
require('FPDF/fpdf.php');

// 1. Ambil & Amankan Parameter
$id_a = mysqli_real_escape_string($koneksi, $_GET['id_anggota'] ?? ($_GET['id'] ?? ''));
$t1 = mysqli_real_escape_string($koneksi, $_GET['tgl_awal'] ?? '');
$t2 = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir'] ?? '');

// 2. Ambil Info Anggota (Hanya jika ID ada)
$nama_anggota = "SEMUA ANGGOTA";
if (!empty($id_a)) {
    $q_info = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$id_a'");
    $d_info = mysqli_fetch_assoc($q_info);
    if ($d_info) {
        $nama_anggota = strtoupper($d_info['nama']);
    } else {
        $id_a = ''; // Reset jika ID ngawur
    }
}

// 3. Query Data (Strict Filter)
$sql = "SELECT s.*, a.nama FROM tb_simpanan_ramdan s 
        INNER JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota 
        WHERE 1=1";

if (!empty($id_a)) {
    $sql .= " AND s.id_anggota = '$id_a'";
}

if (!empty($t1) && !empty($t2)) {
    $sql .= " AND s.tanggal BETWEEN '$t1' AND '$t2'";
} elseif (!empty($t1)) {
    $sql .= " AND s.tanggal >= '$t1'";
} elseif (!empty($t2)) {
    $sql .= " AND s.tanggal <= '$t2'";
}

$sql .= " ORDER BY s.tanggal ASC, s.id_simpanan ASC";
$query = mysqli_query($koneksi, $sql);

// 4. Bangun PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// --- KOP SURAT ---
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 7, 'KOPERASI SIMPAN PINJAM RAMDAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(190, 5, 'Sistem Informasi Pengelolaan Keuangan Koperasi Terpadu', 0, 1, 'C');
$pdf->Cell(190, 5, 'Jl. Permana No. 67, Kota Cimahi | Telp: 0812-3456-7890', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(190, 1, '', 'T', 1); // Garis tebal
$pdf->Cell(190, 1, '', 'B', 1); // Garis tipis
$pdf->Ln(5);

// --- JUDUL LAPORAN ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'LAPORAN MUTASI SIMPANAN', 0, 1, 'C');
$pdf->Ln(2);

// --- INFO DETAIL ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Nama Anggota', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, $nama_anggota, 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'ID Anggota', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, ($id_a ?: '-'), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 6, 'Periode Laporan', 0, 0);
$pdf->Cell(5, 6, ':', 0, 0);
$pdf->SetFont('Arial', '', 10);
$txt_periode = ($t1 ? date('d/m/Y', strtotime($t1)) : 'Awal') . ' s/d ' . ($t2 ? date('d/m/Y', strtotime($t2)) : 'Sekarang');
$pdf->Cell(60, 6, $txt_periode, 0, 1);
$pdf->Ln(5);

// --- HEADER TABEL ---
$pdf->SetFillColor(51, 65, 85); // Slate Dark
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(55, 10, 'Keterangan Transaksi', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Debit (Keluar)', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Kredit (Masuk)', 1, 1, 'C', true);

// --- ISI TABEL ---
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$no = 1;
$total_kredit = 0;
$total_debit = 0;

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C');
        $pdf->Cell(30, 8, date('d-m-Y', strtotime($row['tanggal'])), 1, 0, 'C');
        
        $is_setoran = ($row['jumlah'] > 0);
        $ket = $is_setoran ? 'Setoran: '.$row['jenis_simpanan'] : 'Penarikan Tunai';
        $pdf->Cell(55, 8, ' ' . $ket, 1, 0, 'L');
        
        // Kolom Debit (Penarikan)
        if (!$is_setoran) {
            $pdf->Cell(45, 8, rupiah(abs($row['jumlah'])), 1, 0, 'R');
            $total_debit += abs($row['jumlah']);
        } else {
            $pdf->Cell(45, 8, '-', 1, 0, 'C');
        }
        
        // Kolom Kredit (Setoran)
        if ($is_setoran) {
            $pdf->Cell(50, 8, rupiah($row['jumlah']), 1, 1, 'R');
            $total_kredit += $row['jumlah'];
        } else {
            $pdf->Cell(50, 8, '-', 1, 1, 'C');
        }
    }
    
    // --- FOOTER TABEL ---
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(95, 10, 'TOTAL PER PERIODE', 1, 0, 'C', true); // Perataan diubah ke Center ('C')
    $pdf->Cell(45, 10, rupiah($total_debit), 1, 0, 'R', true);
    $pdf->Cell(50, 10, rupiah($total_kredit), 1, 1, 'R', true);
    
    $pdf->SetFillColor(230, 242, 255);
    $pdf->Cell(95, 10, 'SALDO BERSIH', 1, 0, 'C', true); // Perataan diubah ke Center ('C')
    $pdf->Cell(95, 10, rupiah($total_kredit - $total_debit), 1, 1, 'R', true);

} else {
    $pdf->Cell(190, 15, 'Tidak ada riwayat transaksi pada filter ini.', 1, 1, 'C');
}

// --- TANDA TANGAN ---
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Cimahi, ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(130);
$pdf->Cell(60, 5, 'Petugas Administrasi,', 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(130);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 5, strtoupper($_SESSION['nama']), 0, 1, 'C');
$pdf->Cell(130);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, 'NIP. ...........................', 'T', 1, 'C');

$pdf->Output('I', 'Laporan_Mutasi_' . ($id_a ?: 'Semua') . '.pdf');