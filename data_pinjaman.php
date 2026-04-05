<?php
include 'auth.php';
include 'koneksi.php';

// JOIN tabel pinjaman dan anggota
$query = mysqli_query($koneksi, "
    SELECT p.*, a.nama 
    FROM tb_pinjaman_ramdan p
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota
    ORDER BY p.id_pinjaman DESC
");

// Panggil Header
include 'header.php';
?>

    <h2>Data Pinjaman Anggota</h2>
    <a href="tambah_pinjaman.php" class="btn" style="margin-bottom: 15px;">+ Ajukan Pinjaman Baru</a>
    <a href="cetak_pinjaman.php" target="_blank" class="btn btn-warning" style="margin-bottom: 15px; margin-left: 10px;">🖨️ Cetak Laporan</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jml Pinjaman</th>
                <th>Bunga</th>
                <th>Lama</th>
                <th>Total Pinjaman (Hutang)</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($data = mysqli_fetch_assoc($query)) { 
                // Pewarnaan status
                $warna_status = 'bg-warning';
                if($data['status_pinjaman'] == 'Disetujui') $warna_status = 'bg-primary';
                if($data['status_pinjaman'] == 'Lunas') $warna_status = 'bg-success';
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $data['nama']; ?></td>
                <td>Rp <?php echo number_format($data['jumlah_pinjaman'], 0, ',', '.'); ?></td>
                <td><?php echo $data['bunga']; ?>%</td>
                <td><?php echo $data['lama_pinjaman']; ?> Bln</td>
                <td><strong>Rp <?php echo number_format($data['total_pinjaman'], 0, ',', '.'); ?></strong></td>
                <td><?php echo date('d-m-Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                <td><span class="badge <?php echo $warna_status; ?>"><?php echo $data['status_pinjaman']; ?></span></td>
                <td>
                    <?php 
                    // Tombol ACC hanya muncul kalau statusnya masih 'Diajukan' dan yang login adalah Admin
                    if($data['status_pinjaman'] == 'Diajukan' && $_SESSION['role'] == 'Admin') { ?>
                        <a href="acc_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" 
                           onclick="return confirm('Apakah kamu yakin ingin menyetujui pinjaman ini?')" 
                           class="btn" style="background-color: #0d6efd; color: white;">ACC Pinjaman</a>
                    <?php } else if ($data['status_pinjaman'] == 'Disetujui') {
                        echo "Menunggu Pelunasan";
                    } else if ($data['status_pinjaman'] == 'Lunas') {
                        echo "Selesai";
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>