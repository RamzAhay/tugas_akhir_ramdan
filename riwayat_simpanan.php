<!DOCTYPE html>
<html>
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

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Simpanan Anggota</h1>
        <a href="data_simpanan.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Buku Transaksi: <?php echo htmlspecialchars($nama_anggota); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="10%">No</th>
                            <th width="25%">Tanggal Transaksi</th>
                            <th width="30%">Jenis Simpanan</th>
                            <th width="35%">Nominal (Rp)</th>
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
                        ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $no++; ?></td>
                            <td class="text-center align-middle"><?php echo date('d-m-Y', strtotime($riwayat['tanggal'])); ?></td>
                            <td class="text-center align-middle">
                                <?php if ($jenis == 'Pokok'): ?>
                                    <div style="background-color: #e74a3b; color: #ffffff; padding: 6px 0; border-radius: 5px; font-size: 13px; font-weight: bold; width: 90px; margin: 0 auto; text-align: center;">Pokok</div>
                                <?php elseif ($jenis == 'Wajib'): ?>
                                    <div style="background-color: #f6c23e; color: #000000; padding: 6px 0; border-radius: 5px; font-size: 13px; font-weight: bold; width: 90px; margin: 0 auto; text-align: center;">Wajib</div>
                                <?php else: ?>
                                    <div style="background-color: #1cc88a; color: #ffffff; padding: 6px 0; border-radius: 5px; font-size: 13px; font-weight: bold; width: 90px; margin: 0 auto; text-align: center;">Sukarela</div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right align-middle font-weight-bold">
                                Rp <?php echo number_format($riwayat['jumlah'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="4" class="text-center text-danger font-weight-bold py-4">Belum ada riwayat simpanan untuk anggota ini.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>