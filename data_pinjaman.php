<?php
include 'auth.php';
include 'koneksi.php';

// JOIN tabel pinjaman dan anggota
$query = mysqli_query($koneksi, "
    SELECT p.*, a.nama 
    FROM tb_pinjaman_ramdan p
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota
    ORDER BY p.id_pinjaman ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pinjaman Koperasi</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f8d7da; } /* Warna merah muda biar beda sama simpanan */
        .btn { padding: 5px 10px; text-decoration: none; border: 1px solid black; background: #eee; color: black; }
        .badge { padding: 3px 8px; border-radius: 5px; color: white; }
        .bg-warning { background-color: orange; }
        .bg-success { background-color: green; }
        .bg-primary { background-color: blue; }
    </style>
</head>
<body>

    <h2>Data Pinjaman Anggota</h2>
    
    <?php if($_SESSION['role'] == 'Admin') { ?>
        <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
    <?php } else { ?>
        <a href="dashboard_petugas.php" class="btn">Kembali ke Dashboard</a>
    <?php } ?>

    <br><br>
    <a href="tambah_pinjaman.php" class="btn">+ Ajukan Pinjaman Baru</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jumlah Pinjaman</th>
                <th>Bunga</th>
                <th>Lama</th>
                <th>Total Pinjaman (Hutang)</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($data = mysqli_fetch_assoc($query)) { 
                // Pewarnaan status biar keren
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
</body>
</html>