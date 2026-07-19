<?php
// public/berita.php - Halaman untuk pengunjung
require_once '../config/database.php';

// Ambil parameter
$id_berita = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$limit = 6;
$offset = ($halaman - 1) * $limit;

// Jika membuka detail berita
if ($id_berita > 0) {
    // Update views
    query("UPDATE berita SET views = views + 1 WHERE id = $id_berita");
    
    // Ambil detail berita
    $query_detail = "SELECT * FROM berita WHERE id = $id_berita";
    $result_detail = query($query_detail);
    
    if (mysqli_num_rows($result_detail) == 0) {
        echo "<script>alert('Berita tidak ditemukan!'); window.location.href='berita.php';</script>";
        exit();
    }
    
    $berita = fetch($result_detail);
    
    // Ambil berita terkait
    $query_terkait = "SELECT * FROM berita WHERE id != $id_berita ORDER BY tgl_post DESC LIMIT 3";
    $berita_terkait = fetchAll(query($query_terkait));
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($berita['judul']) ?> - MTs Bahrul Ulum</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
            .berita-detail {
                background: white;
                border-radius: 8px;
                padding: 30px;
                margin-bottom: 30px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .berita-header {
                border-bottom: 2px solid #f0f0f0;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }
            .berita-header h1 {
                color: #2c3e50;
                margin-bottom: 10px;
            }
            .berita-meta {
                color: #7f8c8d;
                font-size: 14px;
            }
            .berita-meta span {
                margin-right: 15px;
            }
            .berita-foto {
                text-align: center;
                margin: 20px 0;
            }
            .berita-foto img {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .berita-isi {
                line-height: 1.8;
                color: #34495e;
            }
            .btn-back {
                display: inline-block;
                background: #3498db;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                margin-bottom: 20px;
            }
            .btn-back:hover {
                background: #2980b9;
            }
            .berita-terkait {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-top: 30px;
            }
            .terkait-list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
                margin-top: 15px;
            }
            .terkait-item {
                background: white;
                padding: 15px;
                border-radius: 8px;
            }
            .terkait-item a {
                color: #3498db;
                text-decoration: none;
            }
            @media (max-width: 768px) {
                .berita-detail { padding: 15px; }
                .berita-header h1 { font-size: 20px; }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="container">
            <a href="berita.php" class="btn-back">← Kembali ke Daftar Berita</a>
            
            <div class="berita-detail">
                <div class="berita-header">
                    <h1><?= htmlspecialchars($berita['judul']) ?></h1>
                    <div class="berita-meta">
                        <span>📅 <?= date('d F Y', strtotime($berita['tgl_post'])) ?></span>
                        <span>🏷️ <?= ucfirst($berita['kategori']) ?></span>
                        <span>👁️ <?= $berita['views'] ?>x dilihat</span>
                    </div>
                </div>
                
                <?php if (!empty($berita['foto']) && file_exists("../" . $berita['foto'])): ?>
                <div class="berita-foto">
                    <img src="../<?= htmlspecialchars($berita['foto']) ?>" alt="<?= htmlspecialchars($berita['judul']) ?>">
                </div>
                <?php endif; ?>
                
                <div class="berita-isi">
                    <?= nl2br(htmlspecialchars($berita['isi'])) ?>
                </div>
            </div>
            
            <?php if (count($berita_terkait) > 0): ?>
            <div class="berita-terkait">
                <h3>Berita Terkait</h3>
                <div class="terkait-list">
                    <?php foreach ($berita_terkait as $terkait): ?>
                    <div class="terkait-item">
                        <a href="berita.php?id=<?= $terkait['id'] ?>">
                            <strong><?= htmlspecialchars($terkait['judul']) ?></strong>
                        </a>
                        <br>
                        <small><?= date('d M Y', strtotime($terkait['tgl_post'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
} else {
    // Tampilan daftar berita
    $kategori_filter = isset($_GET['kategori']) ? escape($_GET['kategori']) : '';
    $search = isset($_GET['search']) ? escape($_GET['search']) : '';
    
    $where = "1=1";
    if ($kategori_filter && $kategori_filter != 'semua') {
        $where .= " AND kategori = '$kategori_filter'";
    }
    if ($search) {
        $where .= " AND (judul LIKE '%$search%' OR isi LIKE '%$search%')";
    }
    
    $total_query = "SELECT COUNT(*) as total FROM berita WHERE $where";
    $total_result = query($total_query);
    $total_berita = fetch($total_result)['total'];
    $total_halaman = ceil($total_berita / $limit);
    
    $query_berita = "SELECT * FROM berita WHERE $where ORDER BY tgl_post DESC LIMIT $offset, $limit";
    $daftar_berita = fetchAll(query($query_berita));
    
    $kategori_list = fetchAll(query("SELECT DISTINCT kategori FROM berita"));
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Berita - MTs Bahrul Ulum</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
            .berita-container {
                display: grid;
                grid-template-columns: 1fr 300px;
                gap: 30px;
            }
            .berita-list {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 25px;
            }
            .berita-card {
                background: white;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                transition: transform 0.3s;
            }
            .berita-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            }
            .berita-card-img {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }
            .berita-card-content {
                padding: 20px;
            }
            .berita-card-category {
                display: inline-block;
                background: #3498db;
                color: white;
                padding: 3px 10px;
                border-radius: 3px;
                font-size: 12px;
                margin-bottom: 10px;
            }
            .berita-card-title {
                font-size: 18px;
                margin-bottom: 10px;
            }
            .berita-card-title a {
                color: #2c3e50;
                text-decoration: none;
            }
            .berita-card-title a:hover {
                color: #3498db;
            }
            .berita-card-excerpt {
                color: #7f8c8d;
                font-size: 14px;
                margin-bottom: 15px;
                line-height: 1.6;
            }
            .berita-card-meta {
                color: #95a5a6;
                font-size: 12px;
                border-top: 1px solid #ecf0f1;
                padding-top: 10px;
            }
            .sidebar-widget {
                background: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 25px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .sidebar-widget h3 {
                margin-bottom: 15px;
                color: #2c3e50;
                border-bottom: 2px solid #3498db;
                padding-bottom: 8px;
            }
            .search-box {
                display: flex;
            }
            .search-box input {
                flex: 1;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px 0 0 4px;
            }
            .search-box button {
                padding: 8px 15px;
                background: #3498db;
                color: white;
                border: none;
                border-radius: 0 4px 4px 0;
                cursor: pointer;
            }
            .kategori-list {
                list-style: none;
                padding: 0;
            }
            .kategori-list li {
                margin-bottom: 10px;
            }
            .kategori-list a {
                color: #34495e;
                text-decoration: none;
                display: block;
                padding: 5px 10px;
                transition: 0.3s;
            }
            .kategori-list a:hover,
            .kategori-list a.active {
                background: #3498db;
                color: white;
                border-radius: 4px;
            }
            .pagination {
                margin-top: 40px;
                text-align: center;
            }
            .pagination a, .pagination span {
                display: inline-block;
                padding: 10px 15px;
                margin: 0 5px;
                background: white;
                color: #3498db;
                text-decoration: none;
                border-radius: 5px;
            }
            .pagination a:hover, .pagination .active {
                background: #3498db;
                color: white;
            }
            @media (max-width: 768px) {
                .berita-container { grid-template-columns: 1fr; }
                .berita-list { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="container">
            <h1>📰 Berita & Pengumuman</h1>
            <p>Informasi terbaru dari MTs Bahrul Ulum NW Telage Bagek</p>
            
            <div class="berita-container">
                <div>
                    <?php if (count($daftar_berita) > 0): ?>
                    <div class="berita-list">
                        <?php foreach ($daftar_berita as $item): ?>
                        <div class="berita-card">
                            <?php if (!empty($item['foto']) && file_exists("../" . $item['foto'])): ?>
                                <img src="../<?= $item['foto'] ?>" alt="<?= htmlspecialchars($item['judul']) ?>" class="berita-card-img">
                            <?php else: ?>
                                <div class="berita-card-img" style="background: #ecf0f1; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #95a5a6;">📷 No Image</span>
                                </div>
                            <?php endif; ?>
                            <div class="berita-card-content">
                                <span class="berita-card-category"><?= ucfirst($item['kategori']) ?></span>
                                <h3 class="berita-card-title">
                                    <a href="berita.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['judul']) ?></a>
                                </h3>
                                <div class="berita-card-excerpt">
                                    <?= substr(strip_tags($item['isi']), 0, 120) ?>...
                                </div>
                                <div class="berita-card-meta">
                                    📅 <?= date('d F Y', strtotime($item['tgl_post'])) ?> 
                                    &nbsp;|&nbsp; 👁️ <?= $item['views'] ?>x
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($total_halaman > 1): ?>
                    <div class="pagination">
                        <?php if ($halaman > 1): ?>
                        <a href="?hal=<?= $halaman-1 ?>&kategori=<?= $kategori_filter ?>&search=<?= urlencode($search) ?>">« Prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                            <?php if ($i == $halaman): ?>
                            <span class="active"><?= $i ?></span>
                            <?php else: ?>
                            <a href="?hal=<?= $i ?>&kategori=<?= $kategori_filter ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($halaman < $total_halaman): ?>
                        <a href="?hal=<?= $halaman+1 ?>&kategori=<?= $kategori_filter ?>&search=<?= urlencode($search) ?>">Next »</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div style="background: white; padding: 40px; text-align: center; border-radius: 8px;">
                        <p>Belum ada berita.</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <div class="sidebar-widget">
                        <h3>Cari Berita</h3>
                        <form method="GET" class="search-box">
                            <input type="text" name="search" placeholder="Cari berita..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">Cari</button>
                        </form>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3>Kategori</h3>
                        <ul class="kategori-list">
                            <li><a href="berita.php" class="<?= !$kategori_filter ? 'active' : '' ?>">Semua Berita</a></li>
                            <?php foreach ($kategori_list as $kat): ?>
                            <li>
                                <a href="?kategori=<?= $kat['kategori'] ?>" class="<?= $kategori_filter == $kat['kategori'] ? 'active' : '' ?>">
                                    <?= ucfirst($kat['kategori']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3>Berita Populer</h3>
                        <?php
                        $query_populer = "SELECT id, judul, views FROM berita ORDER BY views DESC LIMIT 5";
                        $populer_list = fetchAll(query($query_populer));
                        ?>
                        <ul class="kategori-list">
                            <?php foreach ($populer_list as $populer): ?>
                            <li>
                                <a href="berita.php?id=<?= $populer['id'] ?>">
                                    <?= htmlspecialchars($populer['judul']) ?>
                                    <small>(<?= $populer['views'] ?>x)</small>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
}
?>