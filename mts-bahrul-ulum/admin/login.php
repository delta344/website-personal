<?php
require_once '../config/database.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'panitia') {
        redirect("dashboard.php");
    } else {
        redirect("../public/index.php");
    }
}

$error = '';
$success = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $password = md5($_POST['password']); // Menggunakan MD5 sesuai database awal
    
    // Query cek user
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = query($query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = fetch($result);
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        // Redirect berdasarkan role
        if ($user['role'] == 'admin' || $user['role'] == 'panitia') {
            redirect("dashboard.php");
        } else {
            redirect("../public/index.php");
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - MTs Bahrul Ulum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .login-box {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .login-header h2 {
            color: #3498db;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52,152,219,0.3);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .back-to-site {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
        
        .back-to-site:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 25px;
            }
            
            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">🏫</div>
                <h1>MTs Bahrul Ulum</h1>
                <h2>Panel Administrator</h2>
                <p>Silakan login untuk mengakses dashboard</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    ❌ <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" onsubmit="return validateForm()">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn-login">🔐 Login</button>
            </form>
            
            <div class="footer-links">
                <a href="../public/index.php" class="back-to-site">← Kembali ke Website</a>
                <p style="margin-top: 15px;">
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function validateForm() {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();
            
            if (username === '') {
                alert('Username tidak boleh kosong!');
                document.getElementById('username').focus();
                return false;
            }
            
            if (password === '') {
                alert('Password tidak boleh kosong!');
                document.getElementById('password').focus();
                return false;
            }
            
            return true;
        }
        
        // Tambahkan event enter
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>