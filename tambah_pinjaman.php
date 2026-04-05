<?php
include 'auth.php';
include 'koneksi.php';

// Ambil daftar anggota
$query_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajukan Pinjaman</title>
</head>
<body>

    <h2>Form Pengajuan Pinjaman</h2>
    <a href="data_pinjaman.php">Kembali ke Data Pinjaman</a>
    <br><br>

    <form method="POST" action="proses_tambah_pinjaman.php">
        <label>Pilih Anggota:</label><br>
        <select name="id_anggota" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while($anggota = mysqli_fetch_assoc($query_anggota)) { ?>
                <option value="<?php echo $anggota['id_anggota']; ?>">
                    <?php echo $anggota['nama']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Jumlah Pinjaman (Rp):</label><br>
        <input type="number" name="jumlah_pinjaman" required><br><br>

        <label>Bunga (%):</label><br>
        <input type="number" step="0.01" name="bunga" value="5" readonly><br><br>

        <label>Lama Pinjaman (Bulan):</label><br>
        <input type="number" name="lama_pinjaman" required><br><br>

        <button type="submit">Ajukan Pinjaman</button>
    </form>

</body>
</html>