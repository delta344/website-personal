<?php
require_once '../config/database.php';
cekAkses(['admin', 'panitia']);

// Statistik

require_once '../config/database.php';
cekAkses(['admin', 'panitia']);

// Statistik
$total_berita = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM berita"))['total'];
$total_pendaftar = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM ppdb"))['total'];
$total_pengajar = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM pengajar"))['total'];
$total_prestasi = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM kesiswaan WHERE tipe='prestasi'"))['total'];

// Data untuk grafik (pendaftar per bulan)
$query_grafik = "SELECT MONTHNAME(tgl_daftar) as bulan, COUNT(*) as jumlah FROM ppdb 
                 WHERE YEAR(tgl_daftar) = YEAR(CURDATE()) 
                 GROUP BY MONTH(tgl_daftar) ORDER BY MONTH(tgl_daftar)";
$grafik_data = fetchAll(query($query_grafik));

$query = "SELECT 
            DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan,
            COUNT(*) as jumlah 
          FROM ppdb 
          GROUP BY DATE_FORMAT(tgl_daftar, '%Y-%m')
          ORDER BY bulan DESC 
          LIMIT 6";

$result = query($query);
$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = date('F Y', strtotime($row['bulan'] . '-01'));
    $data[] = $row['jumlah'];
}

// Jika tidak ada data, tampilkan pesan
$no_data = empty($labels);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Dashboard</h1>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Berita</h3>
                    <p><?= $total_berita ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pendaftar</h3>
                    <p><?= $total_pendaftar ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pengajar</h3>
                    <p><?= $total_pengajar ?></p>
                </div>
                <div class="stat-card">
                    <h3>Prestasi</h3>
                    <p><?= $total_prestasi ?></p>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="grafikPendaftar"></canvas>
            </div>
        </div>
    </div>
    
    <script>
        
         window.onload = function() {
            const canvas = document.getElementById('grafikPendaftar');
            
            if (canvas) {
                const ctx = canvas.getContext('2d');
                const labels = <?= json_encode($labels) ?>;
                const data = <?= json_encode($data) ?>;
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Pendaftar',
                            data: data,
                            backgroundColor: '#3498db'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            }
        };
    </script>
</body>
</html>
<?php

// Data untuk grafik (pendaftar per bulan)
$query_grafik = "SELECT MONTHNAME(tgl_daftar) as bulan, COUNT(*) as jumlah FROM ppdb 
                 WHERE YEAR(tgl_daftar) = YEAR(CURDATE()) 
                 GROUP BY MONTH(tgl_daftar) ORDER BY MONTH(tgl_daftar)";
$grafik_data = fetchAll(query($query_grafik));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Dashboard</h1>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Berita</h3>
                    <p><?= $total_berita ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pendaftar</h3>
                    <p><?= $total_pendaftar ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pengajar</h3>
                    <p><?= $total_pengajar ?></p>
                </div>
                <div class="stat-card">
                    <h3>Prestasi</h3>
                    <p><?= $total_prestasi ?></p>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="grafikPendaftar"></canvas>
            </div>
        </div>
    </div>
    
    <script>
        const ctx = document.getElementById('grafikPendaftar').getContext('2d');
        const grafikData = <?= json_encode($grafik_data) ?>;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: grafikData.map(item => item.bulan),
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: grafikData.map(item => item.jumlah),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                }]
            }
        });
    </script>
</body>
</html>