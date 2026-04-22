<?php
include 'auth.php'; 
include 'koneksi.php';
include 'header.php'; 
?>

<style>
    .card-custom { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
    .card-header-custom { background-color: #f8f9fc; border-bottom: 2px solid #eaecf4; }
    .table-custom th { background-color: #4e73df; color: #ffffff; text-align: center; vertical-align: middle; }
    .table-custom td { vertical-align: middle; }
    .td-center { text-align: center; }
    .td-right { text-align: right; font-weight: 600; }
    .total-saldo { color: #1cc88a; font-weight: bold; }
</style>

<div class="container-fluid mt-4">
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Rekap Simpanan</h5>
            <div>
                <a href="tambah_simpanan.php" class="btn btn-primary btn-sm shadow-sm">Tambah</a>
                <a href="cetak_simpanan.php" target="_blank" class="btn btn-success btn-sm shadow-sm">Cetak PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered table-hover table-custom" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Anggota</th>
                            <th>Pokok</th>
                            <th>Wajib</th>
                            <th>Sukarela</th>
                            <th>Total Keseluruhan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "
                            SELECT a.id_anggota, a.nama, 
                            SUM(CASE WHEN s.jenis_simpanan = 'Pokok' THEN s.jumlah ELSE 0 END) AS total_pokok,
                            SUM(CASE WHEN s.jenis_simpanan = 'Wajib' THEN s.jumlah ELSE 0 END) AS total_wajib,
                            SUM(CASE WHEN s.jenis_simpanan = 'Sukarela' THEN s.jumlah ELSE 0 END) AS total_sukarela,
                            SUM(s.jumlah) AS total_simpanan
                            FROM tb_anggota_ramdan a
                            LEFT JOIN tb_simpanan_ramdan s ON a.id_anggota = s.id_anggota
                            GROUP BY a.id_anggota, a.nama
                            ORDER BY a.nama ASC
                        ");

                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td class="td-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($data['nama']); ?></strong></td>
                            <td class="td-right">Rp <?php echo number_format($data['total_pokok'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="td-right">Rp <?php echo number_format($data['total_wajib'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="td-right">Rp <?php echo number_format($data['total_sukarela'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="td-right total-saldo">Rp <?php echo number_format($data['total_simpanan'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="td-center">
                                <a href="riwayat_simpanan.php?id=<?php echo $data['id_anggota']; ?>" class="btn btn-info btn-sm">Riwayat</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// Panggil penutup dari file footer yang baru saja kita buat
include 'footer.php'; 
?>