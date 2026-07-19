<?php
require_once '../config/database.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'panitia') {
        redirect('../admin/dashboard.php');
    } else {
        redirect('index.php');
    }
}

$error = '';
$success = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = escape($_POST['username']);
    $password = $_POST['password'];
    
    // Gunakan MD5 untuk mencocokkan password
    $password_md5 = md5($password);
    
    // Query dengan MD5
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password_md5'";
    $result = query($query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = fetch($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin' || $user['role'] == 'panitia') {
            redirect('../admin/dashboard.php');
        } else {
            redirect('index.php');
        }
    } else {
        $error = "Username atau password salah!";
    }
}

// Proses Reset Password (khusus admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $username = escape($_POST['reset_username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password == $confirm_password) {
        $new_password_md5 = md5($new_password);
        $query = "UPDATE users SET password = '$new_password_md5' WHERE username = '$username'";
        if (query($query)) {
            $success = "Password berhasil direset! Silakan login dengan password baru.";
        } else {
            $error = "Gagal mereset password!";
        }
    } else {
        $error = "Password baru tidak sama!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .login-box h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .login-box h3 {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-login {
            width: 100%;
            background: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-login:hover {
            background: #45a049;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
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
        .reset-link {
            text-align: center;
            margin-top: 15px;
        }
        .reset-link a {
            color: #667eea;
            text-decoration: none;
        }
        .reset-panel {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn-reset {
            width: 100%;
            background: #f39c12;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .info-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>🔐 Sistem Informasi Sekolah</h2>
            <h3>MTs Bahrul Ulum NW Telage Bagek</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div
            
            <!-- Form Login -->
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn-login">Login</button>
            </form>
            
            <div class="reset-link">
                <a href="#" onclick="toggleReset()">🔑 Lupa password atau ingin reset?</a>
            </div>
            
            <!-- Panel Reset Password -->
            <div id="resetPanel" class="reset-panel" style="display: none;">
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="reset_username" placeholder="Masukkan username" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="reset_password" class="btn-reset">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function toggleReset() {
        var panel = document.getElementById('resetPanel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
        }
    }
    </script>
</body>
</html>