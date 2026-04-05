<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi Ramdan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #0d6efd, #198754); 
            height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
        }

        .login-container { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.3); 
            width: 100%; 
            max-width: 400px; 
            text-align: center; 
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h2 { margin-bottom: 10px; color: #2c3e50; font-size: 26px;}
        .login-container p { color: #6c757d; margin-bottom: 25px; font-size: 15px; }

        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; color: #495057; font-weight: 600; font-size: 14px;}
        .input-group input { 
            width: 100%; 
            padding: 12px 15px; 
            border: 1px solid #ced4da; 
            border-radius: 6px; 
            font-size: 15px; 
            transition: 0.3s; 
        }
        .input-group input:focus { 
            border-color: #0d6efd; 
            outline: none; 
            box-shadow: 0 0 8px rgba(13,110,253,0.4); 
        }

        .btn-login { 
            width: 100%; 
            padding: 14px; 
            background: #0d6efd; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 10px;
        }
        .btn-login:hover { background: #0b5ed7; transform: scale(1.02); }

        /* Desain Notifikasi */
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 6px; font-size: 14px; font-weight: 500; }
        .alert-danger { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Koperasi Ramdan</h2>
        <p>Silakan masuk ke akun Anda</p>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<div class='alert alert-danger'>⚠️ Login gagal! Username atau Password salah.</div>";
            }else if($_GET['pesan'] == "logout"){
                echo "<div class='alert alert-success'>✅ Anda telah berhasil logout.</div>";
            }else if($_GET['pesan'] == "belum_login"){
                echo "<div class='alert alert-danger'>🔒 Anda harus login terlebih dahulu.</div>";
            }
        }
        ?>

        <form action="" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username Anda..." required autocomplete="off">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password Anda..." required>
            </div>
            <button type="submit" class="btn-login">Masuk Sekarang</button>
        </form>
    </div>

</body>
</html>