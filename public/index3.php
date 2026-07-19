<?php
// public/index.php - Halaman Beranda gaya SMAN 1 Keruak
require_once '../config/database.php';

// Ambil data untuk ditampilkan
$berita_terbaru = fetchAll(query("SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 3"));
$prestasi_terbaru = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'prestasi' ORDER BY tanggal DESC LIMIT 3"));
$pengajar_terbaru = fetchAll(query("SELECT * FROM pengajar WHERE status = 'Aktif' ORDER BY id DESC LIMIT 8"));

// Statistik
$total_siswa = 450; // Sesuaikan dengan data real
$total_guru = count(fetchAll(query("SELECT * FROM pengajar WHERE jabatan = 'Guru' AND status = 'Aktif'")));
$total_ekskul = count(fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'eskul'")));
$total_prestasi = count(fetchAll(query("SELECT * FROM kesiswaan WHERE tipe = 'prestasi'")));

// Ambil profil sekolah
$profil = fetchAll(query("SELECT * FROM profil"));
$visi = '';
$misi = '';
foreach ($profil as $p) {
    if ($p['tipe'] == 'visi') $visi = $p['konten'];
    if ($p['tipe'] == 'misi') $misi = $p['konten'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTs Bahrul Ulum NW Telage Bagek - Madrasah Tsanawiyah Unggulan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ========== HEADER ========== */
        .top-banner {
            background: #dc2626;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo-area h1 {
            font-size: 28px;
            color: #1e3a8a;
            letter-spacing: 1px;
        }

        .logo-area p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .nav-menu {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .nav-menu a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-menu a:hover {
            color: #2563eb;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .hero-badge {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .hero h2 {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .hero p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #f59e0b;
            color: white;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #d97706;
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-outline:hover {
            background: white;
            color: #1e3a8a;
        }

        /* ========== STATS SECTION ========== */
        .stats-section {
            background: white;
            padding: 50px 0;
            margin-top: -30px;
            border-radius: 30px 30px 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }

        .stat-card {
            padding: 20px;
        }

        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            letter-spacing: 1px;
        }

        /* ========== SECTION TITLE ========== */
        .section-title {
            text-align: center;
            margin: 60px 0 30px;
        }

        .section-title h2 {
            font-size: 28px;
            color: #1f2937;
            position: relative;
            display: inline-block;
            padding-bottom: 12px;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #f59e0b;
        }

        /* ========== KEPALA SEKOLAH ========== */
        .kepala-sekolah {
            background: linear-gradient(135deg, #f3f4f6 0%, #fff 100%);
            border-radius: 20px;
            padding: 40px;
            margin: 40px 0;
            display: flex;
            gap: 40px;
            align-items: center;
            flex-wrap: wrap;
        }

        .kepala-foto {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .kepala-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .kepala-sambutan {
            flex: 1;
        }

        .kepala-sambutan h3 {
            font-size: 24px;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .kepala-sambutan p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        /* ========== CARD GRID ========== */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card-title a {
            color: #1f2937;
            text-decoration: none;
        }

        .card-excerpt {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .card-meta {
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }

        /* ========== TEACHER GRID ========== */
        .teacher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .teacher-card {
            background: white;
            border-radius: 16px;
            text-align: center;
            padding: 20px;
            transition: 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .teacher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .teacher-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 3px solid #3b82f6;
        }

        .teacher-name {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .teacher-mapel {
            font-size: 12px;
            color: #6b7280;
        }

        /* ========== FOOTER ========== */
        footer {
            background: #111827;
            color: #9ca3af;
            padding: 50px 0 20px;
            margin-top: 50px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-col h4 {
            color: white;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .footer-col p, .footer-col a {
            color: #9ca3af;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .footer-col a:hover {
            color: white;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #374151;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .hero h2 {
                font-size: 28px;
            }
            .kepala-sekolah {
                text-align: center;
                justify-content: center;
            }
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Banner Pengumuman -->
<div class="top-banner">
    📢 PPDB JALUR AFIRMASI DAN PERPINDAHAN : 2 s.d 4 JUNI 2026
</div>

<!-- Header -->
<div class="main-header">
    <div class="container header-container">
        <div class="logo-area">
            <h1>MTs Bahrul Ulum</h1>
            <p>NW TELAGE BAGEK</p>
        </div>
        <div class="nav-menu">
            <a href="index.php">Beranda</a>
            <a href="profil.php">Profil</a>
            <a href="akademik.php">Akademik</a>
            <a href="kesiswaan.php">Kesiswaan</a>
            <a href="ppdb.php">PPDB</a>
            <a href="berita.php">Berita</a>
            <a href="kontak.php">Kontak</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../admin/dashboard.php">Admin Panel</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-badge">✨ SELAMAT DATANG</div>
        <h2>Membangun Masa Depan di<br>MTs Bahrul Ulum NW Telage Bagek</h2>
        <p>Membangun karakter dan prestasi siswa melalui pendidikan yang inovatif, islami, berbudaya, dan berdaya saing global.</p>
        <div class="hero-buttons">
            <a href="ppdb.php" class="btn-primary">📝 Daftar Sekarang</a>
            <a href="profil.php" class="btn-outline">📖 Lihat Profil</a>
        </div>
    </div>
</section>

<!-- Statistik -->
<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $total_siswa ?></div>
                <div class="stat-label">SISWA AKTIF</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_guru ?></div>
                <div class="stat-label">GURU KOMPETEN</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_ekskul ?></div>
                <div class="stat-label">EKSTRAKURIKULER</div>
            </div>
        </div>
    </div>
</div>

<!-- Sambutan Kepala Sekolah -->
<div class="container">
    <div class="kepala-sekolah">
        <div class="kepala-foto">
            <?php
            $kepsek = fetch(query("SELECT * FROM pengajar WHERE jabatan = 'Kepala Sekolah'"));
            if ($kepsek && !empty($kepsek['foto']) && file_exists("../" . $kepsek['foto'])):
            ?>
                <img src="../<?= $kepsek['foto'] ?>" alt="Kepala Sekolah">
            <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e5e7eb;font-size:48px;">👨‍🏫</div>
            <?php endif; ?>
        </div>
        <div class="kepala-sambutan">
            <h3><?= $kepsek ? htmlspecialchars($kepsek['nama']) : 'Kepala Sekolah' ?>, S.Pd.</h3>
            <p><strong>Kepala MTs Bahrul Ulum NW Telage Bagek</strong></p>
            <p>"Assalamu'alaikum Warahmatullahi Wabarakatuh. Selamat datang di website resmi MTs Bahrul Ulum NW Telage Bagek. Kami berkomitmen untuk memberikan pendidikan terbaik yang memadukan ilmu pengetahuan, teknologi, dan nilai-nilai keislaman. Mari bersama-sama membangun generasi yang beriman, berilmu, dan berakhlak mulia."</p>
            <a href="profil.php" style="color:#3b82f6; text-decoration:none;">Lihat Profil Sekolah →</a>
        </div>
    </div>
</div>

<!-- Berita Terbaru -->
<div class="container">
    <div class="section-title">
        <h2>📰 Berita & Pengumuman</h2>
    </div>
    <div class="card-grid">
        <?php if (count($berita_terbaru) > 0): ?>
            <?php foreach ($berita_terbaru as $berita): ?>
            <div class="card">
                <?php if (!empty($berita['foto']) && file_exists("../" . $berita['foto'])): ?>
                    <img src="../<?= $berita['foto'] ?>" class="card-img" alt="<?= htmlspecialchars($berita['judul']) ?>">
                <?php else: ?>
                    <div class="card-img" style="background:#e5e7eb; display:flex; align-items:center; justify-content:center;">📷</div>
                <?php endif; ?>
                <div class="card-content">
                    <h3 class="card-title"><a href="berita.php?id=<?= $berita['id'] ?>"><?= htmlspecialchars($berita['judul']) ?></a></h3>
                    <div class="card-excerpt"><?= substr(strip_tags($berita['isi']), 0, 100) ?>...</div>
                    <div class="card-meta">📅 <?= date('d M Y', strtotime($berita['tgl_post'])) ?> | 👁️ <?= $berita['views'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card"><div class="card-content">Belum ada berita.</div></div>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="berita.php" class="btn-primary" style="background:#1e3a8a;">Lihat Semua Berita →</a>
    </div>
</div>

<!-- Prestasi Terbaru -->
<div class="container">
    <div class="section-title">
        <h2>🏆 Prestasi Terbaru</h2>
    </div>
    <div class="card-grid">
        <?php if (count($prestasi_terbaru) > 0): ?>
            <?php foreach ($prestasi_terbaru as $prestasi): ?>
            <div class="card">
                <?php if (!empty($prestasi['foto']) && file_exists("../" . $prestasi['foto'])): ?>
                    <img src="../<?= $prestasi['foto'] ?>" class="card-img" alt="<?= htmlspecialchars($prestasi['judul']) ?>">
                <?php else: ?>
                    <div class="card-img" style="background:#e5e7eb; display:flex; align-items:center; justify-content:center;">🏅</div>
                <?php endif; ?>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($prestasi['judul']) ?></h3>
                    <div class="card-excerpt"><?= substr(strip_tags($prestasi['deskripsi']), 0, 100) ?>...</div>
                    <div class="card-meta">🎉 <?= date('d M Y', strtotime($prestasi['tanggal'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card"><div class="card-content">Belum ada prestasi.</div></div>
        <?php endif; ?>
    </div>
</div>

<!-- Tenaga Pengajar -->
<div class="container">
    <div class="section-title">
        <h2>👨‍🏫 Tenaga Pendidik & Kependidikan</h2>
    </div>
    <div class="teacher-grid">
        <?php foreach ($pengajar_terbaru as $guru): ?>
        <div class="teacher-card">
            <?php if (!empty($guru['foto']) && file_exists("../" . $guru['foto'])): ?>
                <img src="../<?= $guru['foto'] ?>" class="teacher-img" alt="<?= htmlspecialchars($guru['nama']) ?>">
            <?php else: ?>
                <div class="teacher-img" style="background:#e5e7eb; display:flex; align-items:center; justify-content:center;">👤</div>
            <?php endif; ?>
            <div class="teacher-name"><?= htmlspecialchars($guru['nama']) ?></div>
            <div class="teacher-mapel"><?= htmlspecialchars($guru['mapel'] ?? 'Tenaga Pendidik') ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align: center; margin-bottom: 40px;">
        <a href="akademik.php" class="btn-primary" style="background:#1e3a8a;">Lihat Semua Guru →</a>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>MTs Bahrul Ulum NW Telage Bagek</h4>
                <p>Jl. Pendidikan No. 123<br>Telage Bagek, Lombok Timur, NTB 83571</p>
                <p>📞 081234567890</p>
                <p>✉️ info@mtsbahrululum.sch.id</p>
            </div>
            <div class="footer-col">
                <h4>Informasi</h4>
                <a href="ppdb.php">Pendaftaran Siswa Baru</a>
                <a href="berita.php">Berita Terkini</a>
                <a href="akademik.php">Kalender Akademik</a>
                <a href="kontak.php">Hubungi Kami</a>
            </div>
            <div class="footer-col">
                <h4>Ikuti Kami</h4>
                <a href="#">▶️ YouTube</a>
                <a href="#">📘 Facebook</a>
                <a href="#">📷 Instagram</a>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> MTs Bahrul Ulum NW Telage Bagek. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>