<?php
include 'auth.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Anggota Baru</title>
</head>
<body>

    <h2>Tambah Data Anggota</h2>
    <a href="data_anggota.php">Kembali ke Data Anggota</a>
    <br><br>

    <form method="POST" action="proses_tambah_anggota.php">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Alamat:</label><br>
        <textarea name="alamat" rows="3" required></textarea><br><br>

        <label>No. HP:</label><br>
        <input type="number" name="no_hp" required><br><br>

        <button type="submit">Simpan Data</button>
    </form>

</body>
</html>