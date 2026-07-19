<?php
require_once '../config/database.php';
$berita_terbaru = fetchAll(query("SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 3"));
$prestasi = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe='prestasi' ORDER BY tanggal DESC LIMIT 3"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>MTs Bahrul Ulum NW Telage Bagek</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>MTs Bahrul Ulum NW Telage Bagek</h1>
        <p>Madrasah Tsanawiyah Unggulan</p>
    </div>
    
    <div class="nav">
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="akademik.php">Akademik</a>
        <a href="kesiswaan.php">Kesiswaan</a>
        <a href="ppdb.php">PPDB</a>
        <a href="berita.php">Berita</a>
        <a href="kontak.php">Kontak</a>
        <?php if (isLoggedIn() && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'panitia')): ?>
            <a href="../admin/dashboard.php" style="background:#4CAF50">Dashboard</a>
        <?php elseif (isLoggedIn()): ?>
            <a href="cek-status.php">Cek Status</a>
            <a href="../logout.php">Logout (<?= $_SESSION['username'] ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <!-- Hero Section -->
        <div class="hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:50px; text-align:center; border-radius:10px; margin-bottom:30px">
            <h2>Selamat Datang di MTs Bahrul Ulum NW Telage Bagek</h2>
            <p>Membentuk Generasi Beriman, Bertaqwa, Berilmu, dan Berakhlak Mulia</p>
            <a href="daftar-ppdb.php" style="display:inline-block; background:#4CAF50; color:white; padding:10px 20px; margin-top:20px; text-decoration:none; border-radius:5px">Daftar PPDB Sekarang</a>
        </div>
        
        <!-- Berita Terbaru -->
        <h2>Berita Terbaru</h2>
        <div class="card-grid">
            <?php foreach ($berita_terbaru as $b): ?>
            <div class="card">
                <?php if ($b['foto']): ?>
                    <img src="../<?= $b['foto'] ?>" alt="<?= $b['judul'] ?>">
                <?php endif; ?>
                <h3><?= htmlspecialchars($b['judul']) ?></h3>
                <small><?= $b['tgl_post'] ?></small>
                <p><?= substr(strip_tags($b['isi']), 0, 150) ?>...</p>
                <a href="berita-detail.php?id=<?= $b['id'] ?>">Baca Selengkapnya →</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Prestasi -->
        <h2>Prestasi Terkini</h2>
        <div class="card-grid">
            <?php foreach ($prestasi as $p): ?>
            <div class="card">
                <?php if ($p['foto']): ?>
                    <img src="../<?= $p['foto'] ?>" alt="<?= $p['judul'] ?>">
                <?php endif; ?>
                <h3>🏆 <?= htmlspecialchars($p['judul']) ?></h3>
                <small>Tanggal: <?= $p['tanggal'] ?></small>
                <p><?= htmlspecialchars($p['deskripsi']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div style="background:#333; color:white; text-align:center; padding:20px; margin-top:30px">
        <p>&copy; 2026 MTs Bahrul Ulum NW Telage Bagek | All Rights Reserved | by suteja asmawandi</p>
    </div>
</body>
</html>