<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Jika tidak ada ID di URL, tendang balik ke halaman data_simpanan
if (!isset($_GET['id'])) {
    echo "<script>alert('Pilih anggota terlebih dahulu!'); window.location='data_simpanan.php';</script>";
    exit();
}

$id_anggota = $_GET['id'];

// Ambil info nama anggota
$query_anggota = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$id_anggota'");
$data_anggota = mysqli_fetch_assoc($query_anggota);
$nama_anggota = $data_anggota['nama'] ?? 'Tidak Diketahui';
?>

<div class="container-fluid mt-4">
    <a href="data_simpanan.php" class="btn btn-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Simpanan
    </a>

    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Riwayat Transaksi Simpanan: <?php echo $nama_anggota; ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Transaksi</th>
                            <th>Jenis Simpanan</th>
                            <th>Nominal</th>
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
                                // Ambil jenis dan rapikan hurufnya
                                $jenis = ucfirst(strtolower($riwayat['jenis_simpanan']));
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($riwayat['tanggal'])); ?></td>
                            <td>
                                <?php if ($jenis == 'Pokok'): ?>
                                    <span style="background-color: #dc3545; color: white; padding: 4px 10px; border-radius: 5px; font-size: 13px; font-weight: bold;"><?php echo $jenis; ?></span>
                                <?php elseif ($jenis == 'Wajib'): ?>
                                    <span style="background-color: #ffc107; color: black; padding: 4px 10px; border-radius: 5px; font-size: 13px; font-weight: bold;"><?php echo $jenis; ?></span>
                                <?php else: ?>
                                    <span style="background-color: #28a745; color: white; padding: 4px 10px; border-radius: 5px; font-size: 13px; font-weight: bold;"><?php echo $jenis; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="font-weight-bold">
                                Rp <?php echo number_format($riwayat['jumlah'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-danger' style='font-weight:bold;'>Belum ada riwayat simpanan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
?>