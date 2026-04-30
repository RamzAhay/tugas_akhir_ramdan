<!DOCTYPE html>
<html>
<?php
include 'auth.php'; 
include 'koneksi.php';
include 'header.php'; 
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Simpanan Anggota</h1>
        <div>
            <a href="tambah_simpanan.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Simpanan</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekap Total Simpanan per Anggota</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Anggota</th>
                            <th>Total Pokok</th>
                            <th>Total Wajib</th>
                            <th>Total Sukarela</th>
                            <th>Total Keseluruhan</th>
                            <th width="15%">Aksi</th>
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
                            <td class="text-center align-middle"><?php echo $no++; ?></td>
                            <td class="align-middle font-weight-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="text-right align-middle">Rp <?php echo number_format($data['total_pokok'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right align-middle">Rp <?php echo number_format($data['total_wajib'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right align-middle">Rp <?php echo number_format($data['total_sukarela'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right align-middle font-weight-bold text-success">
                                Rp <?php echo number_format($data['total_simpanan'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td class="text-center align-middle">
                                <a href="riwayat_simpanan.php?id=<?php echo $data['id_anggota']; ?>" class="btn btn-info btn-sm shadow-sm">
                                    <i class="fas fa-history"></i> Riwayat
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>