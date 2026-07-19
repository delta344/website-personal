<?php
require_once '../config/database.php';
$profil = [];
$result = query("SELECT * FROM profil");
while ($row = fetch($result)) {
    $profil[$row['tipe']] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Sekolah - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Profil MTs Bahrul Ulum NW Telage Bagek</h1>
    </div>
    
    <div class="nav">
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="akademik.php">Akademik</a>
        <a href="kesiswaan.php">Kesiswaan</a>
        <a href="ppdb.php">PPDB</a>
        <a href="berita.php">Berita</a>
        <a href="kontak.php">Kontak</a>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Visi</h2>
            <p><?= nl2br(htmlspecialchars($profil['visi']['konten'] ?? '')) ?></p>
        </div>
        
        <div class="card">
            <h2>Misi</h2>
            <p><?= nl2br(htmlspecialchars($profil['misi']['konten'] ?? '')) ?></p>
        </div>
        
        <div class="card">
            <h2>Sejarah</h2>
            <p><?= nl2br(htmlspecialchars($profil['sejarah']['konten'] ?? '')) ?></p>
        </div>
        
        <div class="card">
            <h2>Fasilitas</h2>
            <p><?= nl2br(htmlspecialchars($profil['fasilitas']['konten'] ?? '')) ?></p>
        </div>
        
        <?php if (!empty($profil['struktur']['foto'])): ?>
        <div class="card">
            <h2>Struktur Organisasi</h2>
            <img src="../<?= $profil['struktur']['foto'] ?>" style="max-width:100%">
        </div>
        <?php endif; ?>
    </div>
</body>
</html>