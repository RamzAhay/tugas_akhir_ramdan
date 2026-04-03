<?php
include 'auth.php';
include 'koneksi.php';

// Ambil semua data anggota untuk ditampilkan di pilihan (dropdown)
$query_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Catat Simpanan</title>
</head>
<body>

    <h2>Catat Transaksi Simpanan Baru</h2>
    <a href="data_simpanan.php">Kembali ke Data Simpanan</a>
    <br><br>

    <form method="POST" action="proses_tambah_simpanan.php">
        <label>Pilih Anggota:</label><br>
        <select name="id_anggota" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while($anggota = mysqli_fetch_assoc($query_anggota)) { ?>
                <option value="<?php echo $anggota['id_anggota']; ?>">
                    <?php echo $anggota['nama']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Jenis Simpanan:</label><br>
        <select name="jenis_simpanan" required>
            <option value="Pokok">Simpanan Pokok</option>
            <option value="Wajib">Simpanan Wajib</option>
            <option value="Sukarela">Simpanan Sukarela</option>
        </select><br><br>

        <label>Jumlah Setoran (Rp):</label><br>
        <input type="number" name="jumlah" placeholder="Contoh: 50000" required><br><br>

        <button type="submit">Simpan Transaksi</button>
    </form>

</body>
</html>