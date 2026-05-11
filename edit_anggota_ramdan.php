<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

$id = $_GET['id'];

// Mengambil data anggota
$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan WHERE id_anggota_ramdan='$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Anggota</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
</head>
<body>

    <h2>Edit Data Anggota</h2>
    <a href="data_anggota_ramdan.php">Kembali ke Data Anggota</a>
    <br><br>

    <form method="POST" action="proses_edit_anggota_ramdan.php">
        <input type="hidden" name="id_anggota_ramdan" value="<?php echo $data['id_anggota_ramdan']; ?>">

        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama_ramdan" value="<?php echo $data['nama_ramdan']; ?>" required><br><br>

        <label>Alamat:</label><br>
        <textarea name="alamat_ramdan" rows="3" required><?php echo $data['alamat_ramdan']; ?></textarea><br><br>

        <label>No. HP:</label><br>
        <input type="number" name="no_hp_ramdan" value="<?php echo $data['no_hp_ramdan']; ?>" required><br><br>
        
        <button type="submit">Update Data</button>
    </form>

</body>
</html>