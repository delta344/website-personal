<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>MTs Bahrul Ulum NW Telage Bagek</h1>
        <p>Madrasah Tsanawiyah Unggulan Berbasis Teknologi</p>
    </div>
    
    <div class="nav">
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="akademik.php">Akademik</a>
        <a href="kesiswaan.php">Kesiswaan</a>
        <a href="ppdb.php">PPDB</a>
        <a href="berita.php">Berita</a>
        <a href="kontak.php">Kontak</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../logout.php" style="float: right;">Logout</a>
        <?php else: ?>
            <a href="login.php" style="float: right;">Login</a>
        <?php endif; ?>
    </div>
</body>
</html>