<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Pilih anggota terlebih dahulu!'); window.location='data_simpanan.php';</script>";
    exit();
}

$id_anggota = $_GET['id'];
$query_anggota = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$id_anggota'");
$data_anggota = mysqli_fetch_assoc($query_anggota);
$nama_anggota = $data_anggota['nama'] ?? 'Tidak Diketahui';
?>

<style>
    .card-history { border: none; width: 1000px; border-radius: 8px; border-left: 4px solid #36b9cc; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
    .table-history th { background-color: #f8f9fc; text-align: center; }
    .td-center { text-align: center; vertical-align: middle; }
    .badge-simpanan { padding: 5px 10px; border-radius: 4px; font-weight: bold; }
    .bg-pokok { background-color: #e74a3b; color: white; }
    .bg-wajib { background-color: #f6c23e; color: black; }
    .bg-sukarela { background-color: #1cc88a; color: white; }
</style>

<div class="container-fluid mt-4">
    <a href="data_simpanan.php" class="btn btn-secondary btn-sm mb-3">Kembali</a>

    <div class="card card-history mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Riwayat Transaksi: <?php echo htmlspecialchars($nama_anggota); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered table-hover table-history" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Transaksi</th>
                            <th>Jenis Simpanan</th>
                            <th>Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query_riwayat = mysqli_query($koneksi, "
                            SELECT * FROM tb_simpanan_ramdan 
                            WHERE id_anggota = '$id_anggota' 
                            ORDER BY tanggal DESC
                        ");

                        if (mysqli_num_rows($query_riwayat) > 0) {
                            while ($riwayat = mysqli_fetch_assoc($query_riwayat)) {
                                $jenis = ucfirst(strtolower($riwayat['jenis_simpanan']));
                                $badge_class = 'bg-secondary';
                                if ($jenis == 'Pokok') $badge_class = 'bg-pokok';
                                elseif ($jenis == 'Wajib') $badge_class = 'bg-wajib';
                                elseif ($jenis == 'Sukarela') $badge_class = 'bg-sukarela';
                        ?>
                        <tr>
                            <td class="td-center"><?php echo $no++; ?></td>
                            <td class="td-center"><?php echo date('d-m-Y', strtotime($riwayat['tanggal'])); ?></td>
                            <td class="td-center">
                                <span class="badge-simpanan <?php echo $badge_class; ?>"><?php echo $jenis; ?></span>
                            </td>
                            <td class="text-right font-weight-bold">
                                Rp <?php echo number_format($riwayat['jumlah'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="4" class="text-center text-danger">Belum ada riwayat simpanan.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>