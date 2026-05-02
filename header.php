<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi mengecek halaman aktif
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
    <title>KSP RAMDAN - Sistem Informasi Koperasi</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CSS MASTER BARU KITA (Yang 100% aman) -->
    <link rel="stylesheet" href="style.css">

    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // UX: Masking Rupiah
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    // UX: Button Loading
    function setBtnLoading(btnId, text = "Sedang Memproses...") {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${text}`;
            btn.closest('form').submit();
        }
    }
    </script>
</head>
<body>

<!-- Navbar Utama -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center text-white" href="dashboard_admin.php">
            <i class="bi bi-piggy-bank-fill me-2 text-primary fs-3"></i>
            <span>KSP RAMDAN</span>
        </a>
        
        <!-- Toggle Mobile -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 align-items-lg-center">
                
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('dashboard_admin.php'); ?>" href="dashboard_admin.php">Dashboard</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('data_anggota.php'); ?>" href="data_anggota.php">Anggota</a>
                </li>

                <!-- MENU ANGSURAN -->
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('data_angsuran.php'); ?>" href="data_angsuran.php">Angsuran</a>
                </li>

                <!-- DROPDOWN NATIVE BOOTSTRAP: SIMPANAN -->
                <li class="nav-item dropdown ms-lg-1">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Simpanan
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0">
                        <li><a class="dropdown-item" href="data_simpanan.php"><i class="bi bi-wallet2 me-2 text-muted"></i> Rekap Saldo</a></li>
                        <li><a class="dropdown-item" href="riwayat_simpanan.php"><i class="bi bi-clock-history me-2 text-muted"></i> Log Transaksi</a></li>
                    </ul>
                </li>

                <!-- DROPDOWN NATIVE BOOTSTRAP: PINJAMAN -->
                <li class="nav-item dropdown ms-lg-1">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Pinjaman
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0">
                        <li><a class="dropdown-item" href="data_pinjaman.php"><i class="bi bi-cash-stack me-2 text-muted"></i> Pinjaman Aktif</a></li>
                        <li><a class="dropdown-item" href="riwayat_pinjaman.php"><i class="bi bi-check-circle me-2 text-muted"></i> Riwayat Selesai</a></li>
                    </ul>
                </li>
            </ul>
            
            <!-- Profil User -->
            <div class="d-flex align-items-center mt-3 mt-lg-0 pb-3 pb-lg-0">
                <div class="text-white me-3 d-none d-lg-block text-end">
                    <small class="opacity-75 d-block" style="font-size: 10px; margin-bottom: -3px;">PENGGUNA</small>
                    <strong style="font-size: 14px;"><?php echo $_SESSION['nama']; ?></strong>
                </div>
                <a href="logout.php" class="btn btn-danger btn-sm px-4 rounded-pill fw-bold shadow-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- 
    PENTING: Bootstrap Bundle JS ditaruh di sini (sebelum body ditutup) 
    agar HTML selesai diproses dulu sebelum JS dropdown dijalankan.
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>