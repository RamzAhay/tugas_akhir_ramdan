<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi sederhana untuk mengecek apakah halaman sedang aktif
function isActive($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSP RAMDAN - Dashboard</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    
    <style>
        /* ============================================================
           FIX NAVIGASI RAMDAN - VERSI TERAKHIR (ANTI-GAGAL)
           ============================================================ */
        
        /* 1. Paksa agar elemen dropdown tidak terpotong oleh overflow */
        .navbar, .navbar-collapse, .container-fluid, .nav-item {
            overflow: visible !important; 
        }

        .navbar {
            z-index: 9999 !important;
            position: sticky !important;
            top: 0;
            background-color: #212529 !important;
        }

        /* 2. Styling Dropdown Custom */
        .rdn-dropdown {
            position: relative;
        }

        .rdn-toggle {
            cursor: pointer !important;
            display: flex;
            align-items: center;
            gap: 5px;
            user-select: none;
        }

        /* Panah kecil */
        .rdn-toggle::after {
            content: "";
            border-top: 5px solid rgba(255,255,255,0.6);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            display: inline-block;
            margin-left: 5px;
        }

        /* Menu yang melayang */
        .rdn-menu {
            display: none; 
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 220px;
            background: #ffffff !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3) !important;
            border-radius: 12px;
            padding: 10px 0;
            margin-top: 8px;
            list-style: none;
            z-index: 100000 !important;
            border: 1px solid #ddd;
        }

        /* Munculkan menu */
        .rdn-menu.aktif {
            display: block !important;
        }

        /* Link dalam menu */
        .rdn-item {
            display: block;
            padding: 12px 20px;
            color: #333 !important;
            text-decoration: none !important;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .rdn-item:hover {
            background-color: #f0f7ff;
            color: #0d6efd !important;
            padding-left: 25px;
        }

        .rdn-divider {
            height: 1px;
            background-color: #eee;
            margin: 8px 0;
        }

        /* Style untuk Menu Aktif */
        .nav-link.active {
            color: #0d6efd !important;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="dashboard_admin.php">KSP <span class="text-primary">RAMDAN</span></a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUtama">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navUtama">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('dashboard_admin.php'); echo isActive('dashboard_petugas.php'); ?>" href="<?php echo ($_SESSION['role'] == 'Admin') ? 'dashboard_admin.php' : 'dashboard_petugas.php'; ?>">Dashboard</a>
                </li>
                
                <?php if($_SESSION['role'] != 'Anggota'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('data_anggota.php'); ?>" href="data_anggota.php">Anggota</a>
                </li>
                <?php endif; ?>

                <!-- DROPDOWN PINJAMAN -->
                <li class="nav-item rdn-dropdown">
                    <a class="nav-link rdn-toggle <?php echo isActive('data_pinjaman.php'); echo isActive('riwayat_pinjaman.php'); ?>" id="btnPinjaman">Pinjaman</a>
                    <ul class="rdn-menu" id="menuPinjaman">
                        <li><a class="rdn-item" href="data_pinjaman.php">Pinjaman Aktif</a></li>
                        <li><a class="rdn-item" href="riwayat_pinjaman.php">Riwayat Pinjaman</a></li>
                        <li class="rdn-divider"></li>
                        <li><a class="rdn-item fw-bold text-primary" href="tambah_pinjaman.php">+ Input Pinjaman</a></li>
                    </ul>
                </li>

                <!-- DROPDOWN SIMPANAN -->
                <li class="nav-item rdn-dropdown">
                    <a class="nav-link rdn-toggle <?php echo isActive('data_simpanan.php'); echo isActive('riwayat_simpanan.php'); ?>" id="btnSimpanan">Simpanan</a>
                    <ul class="rdn-menu" id="menuSimpanan">
                        <li><a class="rdn-item" href="data_simpanan.php">Data Saldo</a></li>
                        <li><a class="rdn-item" href="riwayat_simpanan.php">Log Transaksi</a></li>
                        <li class="rdn-divider"></li>
                        <li><a class="rdn-item fw-bold text-success" href="tambah_simpanan.php">+ Setor Tunai</a></li>
                        <li><a class="rdn-item fw-bold text-danger" href="tarik_simpanan.php">− Tarik Tunai</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('data_angsuran.php'); ?>" href="data_angsuran.php">Angsuran</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <div class="text-white me-3 d-none d-lg-block text-end">
                    <small class="opacity-75">Halo,</small><br>
                    <strong><?php echo $_SESSION['nama']; ?></strong>
                </div>
                <a href="logout.php" class="btn btn-danger btn-sm px-4 rounded-pill">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- SCRIPT KLIK MANUAL (PALING STABIL) -->
<script>
document.addEventListener('click', function(e) {
    const btnPinjaman = document.getElementById('btnPinjaman');
    const menuPinjaman = document.getElementById('menuPinjaman');
    const btnSimpanan = document.getElementById('btnSimpanan');
    const menuSimpanan = document.getElementById('menuSimpanan');

    // Klik Pinjaman
    if (e.target.closest('#btnPinjaman')) {
        menuPinjaman.classList.toggle('aktif');
        menuSimpanan.classList.remove('aktif');
        e.stopPropagation();
    } 
    // Klik Simpanan
    else if (e.target.closest('#btnSimpanan')) {
        menuSimpanan.classList.toggle('aktif');
        menuPinjaman.classList.remove('aktif');
        e.stopPropagation();
    } 
    // Klik di mana saja untuk menutup
    else {
        if(menuPinjaman) menuPinjaman.classList.remove('aktif');
        if(menuSimpanan) menuSimpanan.classList.remove('aktif');
    }
});
</script>

<div class="container-fluid mt-4">