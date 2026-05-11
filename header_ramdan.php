<?php
// Fungsi untuk mengecek menu aktif agar navigasi berwarna biru saat diklik
if (!function_exists('isActive')) {
    function isActive($pages) {
        $current_page = basename($_SERVER['PHP_SELF']);
        if (is_array($pages)) {
            return in_array($current_page, $pages) ? 'active fw-bold text-white' : '';
        }
        return $current_page == $pages ? 'active fw-bold text-white' : '';
    }
}

// Ambil data sesi
$role_user = isset($_SESSION['role']) ? $_SESSION['role'] : 'Petugas';
$nama_display = isset($_SESSION['nama_ramdan']) ? $_SESSION['nama_ramdan'] : (isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSP RAMDAN - Sistem Koperasi</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Link CSS (Tetap style.css sesuai permintaan) -->
    <link rel="stylesheet" href="style.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .navbar { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
        .dropdown-menu { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; }
        .nav-link { transition: all 0.3s ease; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4 sticky-top">
    <div class="container">
        <!-- Dashboard Link Fix pada Logo -->
        <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard_<?php echo strtolower($role_user); ?>_ramdan.php">
            <i class="bi bi-wallet2 me-2 fs-4"></i> KSP RAMDAN
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <!-- Dashboard Link Fix pada Menu -->
                    <a class="nav-link <?php echo isActive(['dashboard_admin_ramdan.php', 'dashboard_petugas_ramdan.php']); ?>" href="dashboard_<?php echo strtolower($role_user); ?>_ramdan.php">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['data_anggota_ramdan.php', 'tambah_anggota_ramdan.php', 'edit_anggota_ramdan.php']); ?>" href="data_anggota_ramdan.php">
                        <i class="bi bi-people-fill me-1"></i> Anggota
                    </a>
                </li>

                <!-- DROPDOWN SIMPANAN -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo isActive(['data_simpanan_ramdan.php', 'riwayat_simpanan_ramdan.php', 'tambah_simpanan_ramdan.php', 'tarik_simpanan_ramdan.php']); ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-piggy-bank-fill me-1"></i> Simpanan
                    </a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="data_simpanan_ramdan.php"><i class="bi bi-wallet me-2"></i> Data Saldo</a></li>
                        <li><a class="dropdown-item" href="riwayat_simpanan_ramdan.php"><i class="bi bi-clock-history me-2"></i> Riwayat Mutasi</a></li>
                    </ul>
                </li>

                <!-- DROPDOWN PINJAMAN (Tombol Persetujuan Dihapus) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo isActive(['data_pinjaman_ramdan.php', 'riwayat_pinjaman_ramdan.php', 'tambah_pinjaman_ramdan.php', 'acc_pinjaman_ramdan.php']); ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-cash-stack me-1"></i> Pinjaman
                    </a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="data_pinjaman_ramdan.php"><i class="bi bi-hourglass-split me-2"></i> Pinjaman Aktif</a></li>
                        <li><a class="dropdown-item" href="riwayat_pinjaman_ramdan.php"><i class="bi bi-check-circle me-2"></i> Riwayat Selesai</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['data_angsuran_ramdan.php', 'tambah_angsuran_ramdan.php']); ?>" href="data_angsuran_ramdan.php">
                        <i class="bi bi-calendar-check-fill me-1"></i> Angsuran
                    </a>
                </li>

                <?php if ($role_user == 'Admin'): ?>
                <li class="nav-item ms-lg-3 border-start ps-lg-3">
                    <a class="nav-link text-warning <?php echo isActive(['data_user_ramdan.php', 'tambah_user_ramdan.php']); ?>" href="data_user_ramdan.php">
                        <i class="bi bi-shield-lock-fill me-1"></i> Pengguna
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <!-- PROFIL & LOGOUT -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-white fw-bold d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <?php echo strtoupper($nama_display); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
                    <li><h6 class="dropdown-header">Level: <?php echo $role_user; ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger fw-bold py-2" href="logout_ramdan.php" onclick="return confirm('Yakin ingin keluar dari sistem?');">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">