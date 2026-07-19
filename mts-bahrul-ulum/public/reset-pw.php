<?php
// File: public/reset-password.php
// Hanya untuk akses lokal/developer

$allowed_ips = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    die("Akses ditolak! Hanya untuk localhost.");
}

require_once '../config/database.php';

$message = '';

// Reset semua password ke default
if (isset($_GET['reset_all'])) {
    query("UPDATE users SET password = MD5('admin123') WHERE role = 'admin'");
    query("UPDATE users SET password = MD5('panitia123') WHERE role = 'panitia'");
    query("UPDATE users SET password = MD5('siswa123') WHERE role = 'siswa'");
    $message = "Semua password telah direset ke default!";
}

// Reset password user tertentu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $new_password = md5($_POST['new_password']);
    
    $query = "UPDATE users SET password = '$new_password' WHERE username = '$username'";
    if (query($query)) {
        $message = "Password untuk user '$username' berhasil direset!";
    } else {
        $message = "User tidak ditemukan!";
    }
}

$users = fetchAll(query("SELECT id, username, role FROM users"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Emergency Password Reset</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f2f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { background: #e74c3c; color: white; padding: 10px; text-decoration: none; display: inline-block; margin: 10px 0; }
        .success { background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Emergency Password Reset</h1>
        
        <?php if ($message): ?>
            <div class="success"><?= $message ?></div>
        <?php endif; ?>
        
        <h2>Reset Password User Tertentu</h2>
        <form method="POST">
            <select name="username" required>
                <option value="">Pilih Username</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['username'] ?>"><?= $u['username'] ?> (<?= $u['role'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <input type="password" name="new_password" placeholder="Password Baru" required>
            <button type="submit">Reset Password</button>
        </form>
        
        <h2>Reset All ke Default</h2>
        <a href="?reset_all=1" class="btn" onclick="return confirm('Yakin reset semua password?')">Reset Semua Password ke Default</a>
        
        <h2>Daftar User & Password Default</h2>
        <table>
            <thead>
                <tr><th>Username</th><th>Role</th><th>Default Password</th></tr>
            </thead>
            <tbody>
                <tr><td>admin</td><td>Admin</td><td>admin123</td></tr>
                <tr><td>panitia1</td><td>Panitia</td><td>panitia123</td></tr>
                <tr><td>siswa1</td><td>Siswa</td><td>siswa123</td></tr>
                <?php foreach ($users as $u): ?>
                    <?php if (!in_array($u['username'], ['admin', 'panitia1', 'siswa1'])): ?>
                    <tr><td><?= $u['username'] ?></td><td><?= $u['role'] ?></td><td>123456</td></tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><a href="login.php">← Kembali ke Login</a></p>
    </div>
</body>
</html>