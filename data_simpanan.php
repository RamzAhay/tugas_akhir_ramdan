<?php
include 'auth.php'; // Atau session_start() jika kamu tidak pakai auth.php
include 'koneksi.php';
include 'header.php'; // Menampilkan sidebar/navbar atas
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Rekap Simpanan Anggota</h6>
            <div>
                <a href="tambah_simpanan.php" class="btn btn-primary btn-sm">Tambah Simpanan</a>
                <a href="cetak_simpanan.php" target="_blank" class="btn btn-success btn-sm">Cetak PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Anggota</th>
                            <th>Total Pokok</th>
                            <th>Total Wajib</th>
                            <th>Total Sukarela</th>
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
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $data['nama']; ?></td>
                            <td>Rp <?php echo number_format($data['total_pokok'] ?? 0, 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($data['total_wajib'] ?? 0, 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($data['total_sukarela'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="font-weight-bold text-success">
                                Rp <?php echo number_format($data['total_simpanan'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td>
                                <a href="riwayat_simpanan.php?id=<?php echo $data['id_anggota']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Lihat Riwayat
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

<?php 
// Sertakan footer admin template kamu (jika ada)
// include 'footer.php'; 
?>