<?php
require_once '../config/database.php';
$kurikulum = fetch(query("SELECT * FROM akademik WHERE tipe='kurikulum'"));
$kalender = fetch(query("SELECT * FROM akademik WHERE tipe='kalender'"));
$pengajar = fetchAll(query("SELECT * FROM pengajar ORDER BY id DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Akademik - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Akademik MTs Bahrul Ulum</h1>
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
            <h2>Kurikulum</h2>
            <p><?= nl2br(htmlspecialchars ($kurikulum['isi'] ?? '')) ?></p>
            <?php if (!empty($kurikulum['tahun_ajaran'])): ?>
                <p><strong>Tahun Ajaran:</strong> <?= $kurikulum['tahun_ajaran'] ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($kalender['file_upload'])): ?>
        <div class="card">
            <h2>Kalender Pendidikan</h2>
            <a href="../<?= $kalender['file_upload'] ?>" target="_blank" class="btn">Download Kalender Pendidikan</a>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Tenaga Pengajar</h2>
            <div class="card-grid">
                <?php foreach ($pengajar as $p): ?>
                <div class="card">
                    <?php if ($p['foto']): ?>
                        <img src="../<?= $p['foto'] ?>" style="width:150px; height:150px; object-fit:cover; border-radius:50%; margin:0 auto; display:block">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($p['nama']) ?></h3>
                    <p><strong>NIP:</strong> <?= $p['nip'] ?></p>
                    <p><strong>Jabatan:</strong> <?= $p['jabatan'] ?></p>
                    <p><strong>Mata Pelajaran:</strong> <?= $p['mapel'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>