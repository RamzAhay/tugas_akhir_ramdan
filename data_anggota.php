<?php
include 'auth.php';
include 'koneksi.php';

$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota DESC");

// Panggil Header!
include 'header.php';
?>

    <h2>Data Anggota Koperasi</h2>
    
    <a href="tambah_anggota.php" class="btn" style="margin-bottom: 15px;">+ Tambah Anggota Baru</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Alamat</th>
                <th>No HP</th>
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
                <td>
                    <a href="edit_anggota.php?id=<?php echo $data['id_anggota']; ?>" class="btn btn-warning">Edit</a>
                    <a href="hapus_anggota.php?id=<?php echo $data['id_anggota']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>