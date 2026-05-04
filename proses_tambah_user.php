<?php
include 'auth.php';
include 'koneksi.php';

if (isset($_POST['submit'])) {
    if ($_SESSION['role'] != 'Admin') {
        die("Akses Ditolak!");
    }

    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password_plain = $_POST['password'];
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    echo "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }</style></head><body>";

    // Validasi Cek Username Ganda
    $cek = mysqli_query($koneksi, "SELECT username FROM tb_user_ramdan WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            Swal.fire({ icon: 'warning', title: 'Username Terpakai!', text: 'Silakan gunakan username lain.', confirmButtonColor: '#0d6efd' })
            .then(() => { window.history.back(); });
        </script></body></html>";
        exit();
    }

    // Untuk keamanan sederhana, kita bisa gunakan MD5 (atau password_hash jika server mendukung)
    // Sesuai standar yang mungkin diajarkan di sekolahmu. Jika sebelumnya login.php pakai md5, ini juga harus md5.
    $password_hashed = md5($password_plain); 

    $query = "INSERT INTO tb_user_ramdan (nama, username, password, role) VALUES ('$nama', '$username', '$password_hashed', '$role')";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Akun {$role} baru telah dibuat.', showConfirmButton: false, timer: 2000 })
            .then(() => { window.location.href = 'data_user.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Database error: " . mysqli_error($koneksi) . "', confirmButtonColor: '#d33' })
            .then(() => { window.history.back(); });
        </script>";
    }
    echo "</body></html>";
}
?>