<?php
require_once '../config/database.php';

// Ambil parameter tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'eskul';

// Ambil data berdasarkan tab
if ($tab == 'eskul') {
    $data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'eskul' ORDER BY created_at DESC"));
    $title = "Ekstrakurikuler";
    $icon = "🎯";
} elseif ($tab == 'osis') {
    $data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'osis' ORDER BY created_at DESC"));
    $title = "Organisasi Siswa (OSIS)";
    $icon = "👥";
} elseif ($tab == 'prestasi') {
    $data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'prestasi' ORDER BY tanggal DESC, created_at DESC"));
    $title = "Galeri Prestasi";
    $icon = "🏆";
} else {
    $data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'eskul' ORDER BY created_at DESC"));
    $title = "Ekstrakurikuler";
    $icon = "🎯";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesiswaan - MTs Bahrul Ulum NW Telage Bagek</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .kesiswaan-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .tab-menu {
            display: flex;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .tab-btn {
            flex: 1;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .tab-btn.active {
            background: #3498db;
            color: white;
        }
        
        .tab-btn:hover:not(.active) {
            background: #e9ecef;
        }
        
        .content-panel {
            display: none;
            animation: fadeIn 0.5s;
        }
        
        .content-panel.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .eskul-grid, .prestasi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .eskul-card, .prestasi-card, .osis-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .eskul-card:hover, .prestasi-card:hover {
            transform: translateY(-5px);
        }
        
        .eskul-img, .prestasi-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .eskul-content, .prestasi-content, .osis-content {
            padding: 20px;
        }
        
        .eskul-title, .osis-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .prestasi-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .prestasi-date {
            color: #7f8c8d;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .eskul-desc, .prestasi-desc, .osis-desc {
            color: #7f8c8d;
            line-height: 1.6;
        }
        
        .osis-list {
            display: grid;
            gap: 20px;
        }
        
        .osis-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .badge-prestasi {
            display: inline-block;
            background: #f39c12;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 10px;
        }
        
        @media (max-width: 768px) {
            .eskul-grid, .prestasi-grid {
                grid-template-columns: 1fr;
            }
            
            .tab-btn {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="kesiswaan-container">
        <h1>Kesiswaan</h1>
        <p>Pengembangan bakat, minat, dan prestasi siswa MTs Bahrul Ulum NW Telage Bagek</p>
        
        <!-- Tab Menu -->
        <div class="tab-menu">
            <button class="tab-btn <?= $tab == 'eskul' ? 'active' : '' ?>" onclick="openTab('eskul')">
                🎯 Ekstrakurikuler
            </button>
            <button class="tab-btn <?= $tab == 'osis' ? 'active' : '' ?>" onclick="openTab('osis')">
                👥 OSIS
            </button>
            <button class="tab-btn <?= $tab == 'prestasi' ? 'active' : '' ?>" onclick="openTab('prestasi')">
                🏆 Prestasi
            </button>
        </div>
        
        <!-- Panel Ekstrakurikuler -->
        <div id="eskul" class="content-panel <?= $tab == 'eskul' ? 'active' : '' ?>">
            <?php
            $eskul_data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'eskul' ORDER BY created_at DESC"));
            if (count($eskul_data) > 0):
            ?>
            <div class="eskul-grid">
                <?php foreach ($eskul_data as $eskul): ?>
                <div class="eskul-card">
                    <?php if ($eskul['foto']): ?>
                    <img src="../<?= $eskul['foto'] ?>" alt="<?= htmlspecialchars($eskul['judul']) ?>" class="eskul-img">
                    <?php endif; ?>
                    <div class="eskul-content">
                        <h3 class="eskul-title"><?= htmlspecialchars($eskul['judul']) ?></h3>
                        <p class="eskul-desc"><?= nl2br(htmlspecialchars($eskul['deskripsi'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <p>Belum ada data ekstrakurikuler.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Panel OSIS -->
        <div id="osis" class="content-panel <?= $tab == 'osis' ? 'active' : '' ?>">
            <?php
            $osis_data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'osis' ORDER BY created_at DESC"));
            if (count($osis_data) > 0):
            ?>
            <div class="osis-list">
                <?php foreach ($osis_data as $osis): ?>
                <div class="osis-item">
                    <h3 class="osis-title"><?= htmlspecialchars($osis['judul']) ?></h3>
                    <p class="osis-desc"><?= nl2br(htmlspecialchars($osis['deskripsi'])) ?></p>
                    <?php if ($osis['foto']): ?>
                    <img src="../<?= $osis['foto'] ?>" alt="<?= htmlspecialchars($osis['judul']) ?>" style="max-width: 100%; margin-top: 15px; border-radius: 8px;">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <p>Belum ada data struktur OSIS.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Panel Prestasi -->
        <div id="prestasi" class="content-panel <?= $tab == 'prestasi' ? 'active' : '' ?>">
            <?php
            $prestasi_data = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'prestasi' ORDER BY tanggal DESC, created_at DESC"));
            if (count($prestasi_data) > 0):
            ?>
            <div class="prestasi-grid">
                <?php foreach ($prestasi_data as $prestasi): ?>
                <div class="prestasi-card">
                    <?php if ($prestasi['foto']): ?>
                    <img src="../<?= $prestasi['foto'] ?>" alt="<?= htmlspecialchars($prestasi['judul']) ?>" class="prestasi-img">
                    <?php endif; ?>
                    <div class="prestasi-content">
                        <span class="badge-prestasi">🏆 Prestasi</span>
                        <h3 class="prestasi-title"><?= htmlspecialchars($prestasi['judul']) ?></h3>
                        <?php if ($prestasi['tanggal']): ?>
                        <div class="prestasi-date">📅 <?= date('d F Y', strtotime($prestasi['tanggal'])) ?></div>
                        <?php endif; ?>
                        <p class="prestasi-desc"><?= nl2br(htmlspecialchars($prestasi['deskripsi'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <p>Belum ada data prestasi.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function openTab(tabName) {
            // Sembunyikan semua panel
            var panels = document.getElementsByClassName('content-panel');
            for (var i = 0; i < panels.length; i++) {
                panels[i].classList.remove('active');
            }
            
            // Nonaktifkan semua tombol
            var btns = document.getElementsByClassName('tab-btn');
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove('active');
            }
            
            // Tampilkan panel yang dipilih
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
            
            // Update URL tanpa reload
            var url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }
        
        // Set active tab berdasarkan URL
        var urlParams = new URLSearchParams(window.location.search);
        var tabParam = urlParams.get('tab');
        if (tabParam && ['eskul', 'osis', 'prestasi'].includes(tabParam)) {
            openTab(tabParam);
        }
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>