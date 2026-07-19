<?php
require_once '../config/database.php';
cekAkses(['admin']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alamat = escape($_POST['alamat']);
    $gmaps_link = escape($_POST['gmaps_link']);
    $wa_number = escape($_POST['wa_number']);
    $email = escape($_POST['email']);
    $facebook = escape($_POST['facebook']);
    $instagram = escape($_POST['instagram']);
    $youtube = escape($_POST['youtube']);
    
    $check = query("SELECT id FROM kontak LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $query = "UPDATE kontak SET alamat='$alamat', gmaps_link='$gmaps_link', wa_number='$wa_number', email='$email', facebook='$facebook', instagram='$instagram', youtube='$youtube'";
    } else {
        $query = "INSERT INTO kontak (alamat, gmaps_link, wa_number, email, facebook, instagram, youtube) VALUES ('$alamat', '$gmaps_link', '$wa_number', '$email', '$facebook', '$instagram', '$youtube')";
    }
    
    if (query($query)) {
        alert("Kontak berhasil diupdate");
        redirect("kontak.php");
    }
}

$kontak = fetch(query("SELECT * FROM kontak LIMIT 1"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Kontak</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen Kontak & Informasi</h1>
            
            <div class="form-card">
                <form method="POST">
                    <div class="form-group">
                        <label>Alamat Lengkap:</label>
                        <textarea name="alamat" rows="3" required><?= htmlspecialchars($kontak['alamat'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Google Maps Embed Link:</label>
                        <input type="text" name="gmaps_link" value="<?= htmlspecialchars($kontak['gmaps_link'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                        <small>Dapatkan link embed dari Google Maps</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Nomor WhatsApp:</label>
                        <input type="text" name="wa_number" value="<?= htmlspecialchars($kontak['wa_number'] ?? '') ?>" placeholder="081234567890">
                    </div>
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($kontak['email'] ?? '') ?>" placeholder="info@sekolah.sch.id">
                    </div>
                    
                    <div class="form-group">
                        <label>Facebook:</label>
                        <input type="text" name="facebook" value="<?= htmlspecialchars($kontak['facebook'] ?? '') ?>" placeholder="username atau link">
                    </div>
                    
                    <div class="form-group">
                        <label>Instagram:</label>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($kontak['instagram'] ?? '') ?>" placeholder="@username">
                    </div>
                    
                    <div class="form-group">
                        <label>YouTube:</label>
                        <input type="text" name="youtube" value="<?= htmlspecialchars($kontak['youtube'] ?? '') ?>" placeholder="channel ID atau link">
                    </div>
                    
                    <button type="submit">Simpan Perubahan</button>
                </form>
            </div>
            
            <!-- Preview -->
            <div class="form-card">
                <h3>Preview Informasi Kontak</h3>
                <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($kontak['alamat'] ?? '')) ?></p>
                <?php if (!empty($kontak['gmaps_link'])): ?>
                    <iframe src="<?= $kontak['gmaps_link'] ?>" width="100%" height="300" style="border:0;" allowfullscreen></iframe>
                <?php endif; ?>
                <p><strong>WhatsApp:</strong> <a href="https://wa.me/<?= $kontak['wa_number'] ?? '' ?>"><?= $kontak['wa_number'] ?? '' ?></a></p>
                <p><strong>Email:</strong> <a href="mailto:<?= $kontak['email'] ?? '' ?>"><?= $kontak['email'] ?? '' ?></a></p>
            </div>
        </div>
    </div>
</body>
</html>