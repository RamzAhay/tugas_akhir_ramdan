<?php
// Menentukan link dashboard dan warna navbar secara otomatis berdasarkan Role yang sedang login
$is_admin = ($_SESSION['role'] == 'Admin');
$dashboard_link = $is_admin ? 'dashboard_admin.php' : 'dashboard_petugas.php';
$role_label     = $is_admin ? '' : '(Petugas)';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Koperasi Ramdan</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
</head>
<body class="role-<?php echo strtolower($is_admin ? 'admin' : 'petugas'); ?>">

    <nav class="navbar">
        <div class="container">
            <a href="<?php echo $dashboard_link; ?>" class="navbar-brand">
                <div class="brand-icon">💰</div>
                <div class="brand-text">
                    <div class="brand-name">Koperasi Ramdan</div>
                    <div class="brand-sub"><?php echo $is_admin ? 'Admin' : 'Petugas'; ?></div>
                </div>
            </a>
            <div class="nav-links">
                <a href="<?php echo $dashboard_link; ?>">Dashboard</a>
                <a href="data_anggota.php">Data Anggota</a>
                <a href="data_simpanan.php">Simpanan</a>
                <a href="data_pinjaman.php">Pinjaman</a>
                <a href="riwayat_pinjaman.php" class="nav-link">Riwayat Pinjaman</a>
                <a href="data_angsuran.php">Angsuran</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="content-area">