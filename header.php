<?php
// Menentukan link dashboard dan warna navbar secara otomatis berdasarkan Role yang sedang login
$is_admin = ($_SESSION['role'] == 'Admin');
$dashboard_link = $is_admin ? 'dashboard_admin.php' : 'dashboard_petugas.php';
$role_label     = $is_admin ? '' : '(Petugas)';
$nav_color      = $is_admin ? '#0d6efd' : '#198754'; // Biru untuk Admin, Hijau untuk Petugas
$nav_hover      = $is_admin ? '#0b5ed7' : '#146c43';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aplikasi Koperasi Ramdan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; }
        
        /* --- Desain Navbar --- */
        .navbar { background-color: <?php echo $nav_color; ?>; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 22px; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { background-color: <?php echo $nav_hover; ?>; }
        .btn-logout { background-color: #dc3545 !important; }
        .btn-logout:hover { background-color: #bb2d3b !important; }

        /* --- Desain Container --- */
        .container { padding: 30px; }
        
        /* --- Desain Tabel Modern --- */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px 15px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; text-transform: uppercase; font-size: 14px; }
        tr:hover { background-color: #f1f1f1; }

        /* --- Desain Tombol --- */
        .btn { display: inline-block; padding: 8px 15px; text-decoration: none; border-radius: 5px; border: none; font-size: 14px; cursor: pointer; transition: 0.3s; color: white; background-color: #0d6efd; }
        .btn:hover { background-color: #0b5ed7; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        
        /* --- Desain Form Input --- */
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); max-width: 600px; margin-top: 20px; }
        input[type=text], input[type=number], select, textarea { width: 100%; padding: 10px; margin: 8px 0 20px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button[type=submit] { background-color: #198754; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button[type=submit]:hover { background-color: #146c43; }
        
        /* Badge Status */
        .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        
        /* Desain Kartu Dashboard */
        .card-container { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-top: 4px solid #0d6efd; }
        .card h3 { margin-top: 0; color: #6c757d; font-size: 16px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; color: #212529; margin-bottom: 0; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>🏢 Koperasi Ramdan <?php echo $role_label; ?></h2>
        <div class="nav-links">
            <a href="<?php echo $dashboard_link; ?>">Dashboard</a>
            <a href="data_anggota.php">Data Anggota</a>
            <a href="data_simpanan.php">Simpanan</a>
            <a href="data_pinjaman.php">Pinjaman</a>
            <a href="data_angsuran.php">Angsuran</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">