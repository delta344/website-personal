<?php
require_once '../config/database.php';

// Cek apakah user sudah login sebagai siswa
$is_siswa = isset($_SESSION['role']) && $_SESSION['role'] == 'siswa';
$cek_status = isset($_GET['cek_status']) ? true : false;

// Proses pendaftaran
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar'])) {
    $nama = escape($_POST['nama']);
    $nisn = escape($_POST['nisn']);
    $tempat_lahir = escape($_POST['tempat_lahir']);
    $tanggal_lahir = escape($_POST['tanggal_lahir']);
    $alamat = escape($_POST['alamat']);
    $no_hp = escape($_POST['no_hp']);
    $asal_sekolah = escape($_POST['asal_sekolah']);
    
    // Validasi NISN unik
    $cek_nisn = query("SELECT id FROM ppdb WHERE nisn = '$nisn'");
    if (mysqli_num_rows($cek_nisn) > 0) {
        $error = "NISN sudah terdaftar!";
    } else {
        // Generate no pendaftaran unik
        $no_pendaftaran = "PPDB-" . date("Ymd") . "-" . rand(1000, 9999);
        
        $query_sql = "INSERT INTO ppdb (no_pendaftaran, nama_lengkap, nisn, tempat_lahir, tanggal_lahir, alamat, no_hp, asal_sekolah) 
                  VALUES ('$no_pendaftaran', '$nama', '$nisn', '$tempat_lahir', '$tanggal_lahir', '$alamat', '$no_hp', '$asal_sekolah')";
        
        $result = query($query_sql);
        
        if ($result) {
            $success = "Pendaftaran berhasil! No Pendaftaran: <strong>$no_pendaftaran</strong><br>Silakan simpan nomor pendaftaran untuk cek status kelulusan.";
        } else {
            global $conn;
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}


// Proses cek status
$status_result = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cek_status'])) {
    $no_pendaftaran = escape($_POST['no_pendaftaran']);
    $query_status = "SELECT * FROM ppdb WHERE no_pendaftaran = '$no_pendaftaran'";
    $result_status = query($query_status);
    
    if (mysqli_num_rows($result_status) > 0) {
        $status_result = fetch($result_status);
    } else {
        $status_error = "Nomor pendaftaran tidak ditemukan!";
    }
}

// Statistik PPDB
$total_pendaftar = fetch(query("SELECT COUNT(*) as total FROM ppdb"))['total'];
$tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Online - MTs Bahrul Ulum NW Telage Bagek</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .ppdb-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .info-banner {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .info-banner h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .deadline {
            background: #f39c12;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            margin-top: 15px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .tab {
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
        
        .tab.active {
            background: #3498db;
            color: white;
        }
        
        .tab-panel {
            display: none;
            animation: fadeIn 0.5s;
        }
        
        .tab-panel.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-card, .info-card, .syarat-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .form-group input, 
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn-daftar {
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
        }
        
        .btn-daftar:hover {
            background: #229954;
        }
        
        .syarat-list {
            list-style: none;
            padding: 0;
        }
        
        .syarat-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        
        .syarat-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
            font-size: 18px;
        }
        
        .status-card {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #f39c12;
            color: white;
        }
        
        .status-verifikasi {
            background: #3498db;
            color: white;
        }
        
        .status-lulus {
            background: #27ae60;
            color: white;
        }
        
        .status-tidak_lulus {
            background: #e74c3c;
            color: white;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .info-banner h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="ppdb-container">
        <div class="info-banner">
            <h1>PPDB Online <?= $tahun_ajaran ?></h1>
            <p>Penerimaan Peserta Didik Baru MTs Bahrul Ulum NW Telage Bagek</p>
            <div class="deadline">📅 Pendaftaran: Januari - Juni <?= date('Y') ?></div>
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?= $total_pendaftar ?></div>
                <div>Pendaftar Aktif</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">120</div>
                <div>Kuota Tersedia</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= 120 - $total_pendaftar ?></div>
                <div>Sisa Kuota</div>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab <?= !$cek_status ? 'active' : '' ?>" onclick="openTab('daftar')">📝 Form Pendaftaran</button>
            <button class="tab <?= $cek_status ? 'active' : '' ?>" onclick="openTab('cek')">🔍 Cek Status</button>
            <button class="tab" onclick="openTab('info')">ℹ️ Informasi</button>
            <button class="tab" onclick="openTab('syarat')">📋 Syarat</button>
        </div>
        
        <!-- Panel Form Pendaftaran -->
        <div id="daftar" class="tab-panel <?= !$cek_status ? 'active' : '' ?>">
            <div class="form-card">
                <h2>Formulir Pendaftaran Online</h2>
                
                <?php if ($success): ?>
                <div class="alert alert-success" style="background:#d4edda; padding:15px; border-radius:5px; margin-bottom:20px;">
                    <?= $success ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-error" style="background:#f8d7da; padding:15px; border-radius:5px; margin-bottom:20px; color:#721c24;">
                    <?= $error ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateForm()">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap *</label>
                            <input type="text" name="nama" id="nama" required>
                        </div>
                        <div class="form-group">
                            <label>NISN *</label>
                            <input type="text" name="nisn" id="nisn" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tempat Lahir *</label>
                            <input type="text" name="tempat_lahir" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat Lengkap *</label>
                        <textarea name="alamat" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nomor HP/WhatsApp *</label>
                            <input type="tel" name="no_hp" id="no_hp" required>
                        </div>
                        <div class="form-group">
                            <label>Asal Sekolah *</label>
                            <input type="text" name="asal_sekolah" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="daftar" class="btn-daftar">🚀 Daftar Sekarang</button>
                </form>
            </div>
        </div>
        
        <!-- Panel Cek Status -->
        <div id="cek" class="tab-panel <?= $cek_status ? 'active' : '' ?>">
            <div class="form-card">
                <h2>Cek Status Pendaftaran</h2>
                <p>Masukkan nomor pendaftaran yang Anda terima saat mendaftar</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Nomor Pendaftaran</label>
                        <input type="text" name="no_pendaftaran" placeholder="Contoh: PPDB-20240101-1234" required>
                    </div>
                    <button type="submit" name="cek_status" class="btn-daftar" style="background:#3498db;">🔍 Cek Status</button>
                </form>
                
                <?php if (isset($status_error)): ?>
                <div class="alert alert-error" style="background:#f8d7da; padding:15px; border-radius:5px; margin-top:20px;">
                    <?= $status_error ?>
                </div>
                <?php endif; ?>
                
                <?php if ($status_result): ?>
                <div class="status-card">
                    <h3>Informasi Pendaftar</h3>
                    <p><strong>No. Pendaftaran:</strong> <?= $status_result['no_pendaftaran'] ?></p>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($status_result['nama_lengkap']) ?></p>
                    <p><strong>NISN:</strong> <?= $status_result['nisn'] ?></p>
                    <p><strong>Tanggal Daftar:</strong> <?= date('d F Y', strtotime($status_result['tgl_daftar'])) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-<?= $status_result['status'] ?>">
                            <?= strtoupper(str_replace('_', ' ', $status_result['status'])) ?>
                        </span>
                    </p>
                    
                    <?php if ($status_result['status'] == 'lulus'): ?>
                    <div style="margin-top: 20px; padding: 15px; background:#d4edda; border-radius:5px;">
                        <strong>🎉 SELAMAT!</strong> Anda dinyatakan LULUS. Silakan lakukan daftar ulang dengan membawa berkas asli.
                    </div>
                    <?php elseif ($status_result['status'] == 'tidak_lulus'): ?>
                    <div style="margin-top: 20px; padding: 15px; background:#f8d7da; border-radius:5px;">
                        <strong>Mohon maaf,</strong> Anda belum dinyatakan lulus. Tetap semangat dan coba lagi tahun depan.
                    </div>
                    <?php elseif ($status_result['status'] == 'verifikasi'): ?>
                    <div style="margin-top: 20px; padding: 15px; background:#cce5ff; border-radius:5px;">
                        <strong>📝 Status: Verifikasi Berkas</strong> Berkas Anda sedang diverifikasi oleh panitia.
                    </div>
                    <?php else: ?>
                    <div style="margin-top: 20px; padding: 15px; background:#fff3cd; border-radius:5px;">
                        <strong>⏳ Status: Pending</strong> Pendaftaran Anda telah diterima, menunggu proses verifikasi.
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Panel Informasi -->
        <div id="info" class="tab-panel">
            <div class="info-card">
                <h2>Informasi PPDB</h2>
                
                <h3>📅 Jadwal Pendaftaran</h3>
                <ul class="syarat-list">
                    <li>Pendaftaran Online: 1 Januari - 30 Juni <?= date('Y') ?></li>
                    <li>Verifikasi Berkas: 1 - 15 Juli <?= date('Y') ?></li>
                    <li>Pengumuman Kelulusan: 20 Juli <?= date('Y') ?></li>
                    <li>Daftar Ulang: 21 - 30 Juli <?= date('Y') ?></li>
                    <li>Mulai Belajar: 15 Juli <?= date('Y') ?></li>
                </ul>
                
                <h3>💰 Biaya Pendaftaran</h3>
                <ul class="syarat-list">
                    <li>Formulir Pendaftaran: GRATIS</li>
                    <li>Uang Pangkal: Rp 500.000 (dapat dicicil)</li>
                    <li>SPP per Bulan: Rp 150.000</li>
                </ul>
                
                <h3>🏫 Fasilitas</h3>
                <ul class="syarat-list">
                    <li>Ruangan Ber-AC</li>
                    <li>Laboratorium Komputer</li>
                    <li>Perpustakaan Digital</li>
                    <li>Lapangan Olahraga</li>
                    <li>Asrama (Opsi)</li>
                </ul>
            </div>
        </div>
        
        <!-- Panel Syarat -->
        <div id="syarat" class="tab-panel">
            <div class="syarat-card">
                <h2>Persyaratan Pendaftaran</h2>
                
                <h3>📄 Berkas yang harus disiapkan:</h3>
                <ul class="syarat-list">
                    <li>Fotokopi Ijazah SD/MI (2 lembar)</li>
                    <li>Fotokopi SKHUN (2 lembar)</li>
                    <li>Fotokopi Akta Kelahiran (2 lembar)</li>
                    <li>Fotokopi Kartu Keluarga (2 lembar)</li>
                    <li>Pas foto 3x4 (4 lembar)</li>
                    <li>Fotokopi KIP/KPS (jika ada)</li>
                </ul>
                
                <h3>📝 Persyaratan Khusus:</h3>
                <ul class="syarat-list">
                    <li>Lulusan SD/MI/sederajat tahun berjalan atau 2 tahun sebelumnya</li>
                    <li>Usia maksimal 18 tahun</li>
                    <li>Sehat jasmani dan rohani</li>
                    <li>Bersedia mengikuti peraturan sekolah</li>
                </ul>
                
                <div style="margin-top: 30px; padding: 15px; background:#e8f4fd; border-radius:5px;">
                    <strong>ℹ️ Catatan:</strong> Berkas lengkap dapat diunggah setelah pendaftaran online dan akan diverifikasi oleh panitia.
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openTab(tabName) {
            // Sembunyikan semua panel
            var panels = document.getElementsByClassName('tab-panel');
            for (var i = 0; i < panels.length; i++) {
                panels[i].classList.remove('active');
            }
            
            // Nonaktifkan semua tombol
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Tampilkan panel yang dipilih
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
            
            // Update URL tanpa reload
            var url = new URL(window.location.href);
            if (tabName === 'cek') {
                url.searchParams.set('cek_status', '1');
            } else {
                url.searchParams.delete('cek_status');
            }
            window.history.pushState({}, '', url);
        }
        
        // Validasi form
        function validateForm() {
            var nama = document.getElementById('nama');
            if (nama.value.trim() === '') {
                alert('Nama lengkap harus diisi');
                nama.focus();
                return false;
            }
            
            var nisn = document.getElementById('nisn');
            if (nisn.value.trim() === '') {
                alert('NISN harus diisi');
                nisn.focus();
                return false;
            }
            
            var nohp = document.getElementById('no_hp');
            var hpRegex = /^[0-9]{10,13}$/;
            if (!hpRegex.test(nohp.value)) {
                alert('Nomor HP tidak valid (10-13 digit angka)');
                nohp.focus();
                return false;
            }
            
            return true;
        }
        
        // Set active tab berdasarkan URL
        var urlParams = new URLSearchParams(window.location.search);
        var cekStatus = urlParams.get('cek_status');
        if (cekStatus === '1') {
            // Trigger click on cek tab
            var cekTab = document.querySelector('.tab:nth-child(2)');
            if (cekTab) cekTab.click();
        }
    </script>
</body>
</html>