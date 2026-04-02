<?php
include 'auth.php'; // Pastikan user sudah login
include 'koneksi.php';

// Ambil data anggota dari database
$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota Koperasi</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; text-decoration: none; border: 1px solid black; background: #eee; color: black; }
    </style>
</head>
<body>

    <h2>Data Anggota Koperasi</h2>
    
    <?php if($_SESSION['role'] == 'Admin') { ?>
        <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
    <?php } else { ?>
        <a href="dashboard_petugas.php" class="btn">Kembali ke Dashboard</a>
    <?php } ?>

    <br><br>
    <a href="tambah_anggota.php" class="btn">+ Tambah Anggota Baru</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Alamat</th>
                <th>No. HP</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($data = mysqli_fetch_assoc($query)) { 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $data['nama']; ?></td>
                <td><?php echo $data['alamat']; ?></td>
                <td><?php echo $data['no_hp']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($data['tanggal_daftar'])); ?></td>
                <td>
                    <a href="edit_anggota.php?id=<?php echo $data['id_anggota']; ?>">Edit</a> | 
                    <a href="hapus_anggota.php?id=<?php echo $data['id_anggota']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>