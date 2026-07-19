<?php
// File: admin/sidebar.php
// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            left: 0;
            top: 0;
            z-index: 100;
        }
        .sidebar h2 {
            padding: 20px;
            text-align: center;
            font-size: 18px;
            margin: 0;
            background: #1a252f;
        }
        .sidebar h3 {
            padding: 10px;
            text-align: center;
            font-size: 14px;
            margin: 0;
            color: #bdc3c7;
        }
        .sidebar .user-info {
            padding: 10px 20px;
            background: #1a252f;
            margin: 10px 0;
            font-size: 12px;
            text-align: center;
        }
        .sidebar hr {
            margin: 10px 0;
            border-color: #34495e;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
            padding-left: 30px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            background: #ecf0f1;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>🏫 Admin Panel</h2>
    <h3>MTs Bahrul Ulum</h3>
    <div class="user-info">
        👤 <?= htmlspecialchars($username) ?> <br>
        🔑 Role: <?= ucfirst($role) ?>
    </div>
    <hr>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="profil.php">📄 Profil Sekolah</a>
    <a href="akademik.php">📚 Akademik</a>
    <a href="kesiswaan.php">🎓 Kesiswaan</a>
    <a href="ppdb.php">📝 Manajemen PPDB</a>
    <a href="berita.php">📰 Berita & Pengumuman</a>
    <a href="kontak.php">📞 Kontak</a>
    <?php if ($role == 'admin'): ?>
    <a href="users.php">👥 Manajemen User</a>
    <?php endif; ?>
    <hr>
    <a href="../logout.php" style="color: #e74c3c;">🚪 Logout</a>
</div>
</body>
</html>