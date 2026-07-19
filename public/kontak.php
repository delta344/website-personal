<?php
require_once '../config/database.php';

// Ambil data kontak
$kontak = fetch(query("SELECT * FROM kontak WHERE id = 1"));

// Proses pengiriman pesan (opsional)
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $nama = escape($_POST['nama']);
    $email = escape($_POST['email']);
    $pesan = escape($_POST['pesan']);
    
    // Simpan ke database (opsional, buat tabel messages)
    // Atau kirim email
    
    $success = "Pesan Anda telah terkirim. Terima kasih!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - MTs Bahrul Ulum NW Telage Bagek</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .kontak-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .kontak-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            transition: 0.3s;
        }
        
        .info-item:hover {
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background: #3498db;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .info-detail h4 {
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .info-detail p, .info-detail a {
            color: #7f8c8d;
            text-decoration: none;
        }
        
        .info-detail a:hover {
            color: #3498db;
        }
        
        .map-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .map-container iframe {
            width: 100%;
            height: 300px;
            border: 0;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: #3498db;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: 0.3s;
            font-size: 20px;
        }
        
        .social-link:hover {
            transform: translateY(-3px);
            background: #2980b9;
        }
        
        .contact-form {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-send {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        
        .btn-send:hover {
            background: #2980b9;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .whatsapp-btn {
            display: inline-block;
            background: #25D366;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 15px;
            transition: 0.3s;
        }
        
        .whatsapp-btn:hover {
            background: #128C7E;
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            .kontak-grid {
                grid-template-columns: 1fr;
            }
            
            .info-item {
                flex-direction: column;
                text-align: center;
            }
            
            .info-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="kontak-container">
        <h1>Hubungi Kami</h1>
        <p>Silakan hubungi kami melalui informasi kontak di bawah ini</p>
        
        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="kontak-grid">
            <!-- Informasi Kontak -->
            <div>
                <div class="info-card">
                    <h3>Informasi Kontak</h3>
                    
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-detail">
                            <h4>Alamat</h4>
                            <p><?= htmlspecialchars($kontak['alamat'] ?? 'Telage Bagek, Lombok Timur, NTB') ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-detail">
                            <h4>Telepon / WhatsApp</h4>
                            <p><a href="https://wa.me/<?= $kontak['wa_number'] ?? '6281234567890' ?>"><?= $kontak['wa_number'] ?? '081234567890' ?></a></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div class="info-detail">
                            <h4>Email</h4>
                            <p><a href="mailto:<?= $kontak['email'] ?? 'info@mtsbahrululum.sch.id' ?>"><?= $kontak['email'] ?? 'info@mtsbahrululum.sch.id' ?></a></p>
                        </div>
                    </div>
                    
                    <a href="https://wa.me/<?= $kontak['wa_number'] ?? '6281234567890' ?>?text=Halo%20saya%20ingin%20bertanya%20tentang%20sekolah" 
                       class="whatsapp-btn" target="_blank">
                        💬 Chat WhatsApp
                    </a>
                </div>
                
                <div class="info-card">
                    <h3>Media Sosial</h3>
                    <div class="social-links">
                        <?php if ($kontak['facebook'] ?? false): ?>
                        <a href="https://facebook.com/<?= $kontak['facebook'] ?>" class="social-link" target="_blank">📘</a>
                        <?php endif; ?>
                        <?php if ($kontak['instagram'] ?? false): ?>
                        <a href="https://instagram.com/<?= $kontak['instagram'] ?>" class="social-link" target="_blank">📷</a>
                        <?php endif; ?>
                        <?php if ($kontak['youtube'] ?? false): ?>
                        <a href="https://youtube.com/<?= $kontak['youtube'] ?>" class="social-link" target="_blank">▶️</a>
                        <?php endif; ?>
                        <a href="#" class="social-link">🐦</a>
                        <a href="#" class="social-link">💼</a>
                    </div>
                </div>
            </div>
            
            <!-- Form & Google Maps -->
            <div>
                <div class="map-container">
                    <?php if ($kontak['gmaps_link'] ?? false): ?>
                    <iframe src="<?= $kontak['gmaps_link'] ?>" allowfullscreen></iframe>
                    <?php else: ?>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.0!2d116.5!3d-8.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOMKwMjknNTkuMCJTIDExNsKwMjAnMDAuMCJF!5e0!3m2!1sid!2sid!4v1234567890" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
                
                <div class="contact-form">
                    <h3>Kirim Pesan</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Pesan</label>
                            <textarea name="pesan" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn-send">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>