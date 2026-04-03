<?php
include 'auth.php';
include 'koneksi.php';

// Menangkap ID dari URL
$id = $_GET['id'];

// Mengambil data anggota berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan WHERE id_anggota='$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Anggota</title>
</head>
<body>

    <h2>Edit Data Anggota</h2>
    <a href="data_anggota.php">Kembali ke Data Anggota</a>
    <br><br>

    <form method="POST" action="proses_edit_anggota.php">
        <input type="hidden" name="id_anggota" value="<?php echo $data['id_anggota']; ?>">

        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" value="<?php echo $data['nama']; ?>" required><br><br>

        <label>Alamat:</label><br>
        <textarea name="alamat" rows="3" required><?php echo $data['alamat']; ?></textarea><br><br>

        <label>No. HP:</label><br>
        <input type="number" name="no_hp" value="<?php echo $data['no_hp']; ?>" required><br><br>
        
        <button type="submit">Update Data</button>
    </form>

</body>
</html>