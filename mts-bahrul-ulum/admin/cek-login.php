<?php
// admin/cek_login.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = query($query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = fetch($result);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin' || $user['role'] == 'panitia') {
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: ../public/index.php");
            exit();
        }
    } else {
        header("Location: login.php?error=1");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>