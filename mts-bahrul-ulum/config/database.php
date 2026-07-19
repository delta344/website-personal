<?php
// Memulai session untuk login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'db_mts_bahrul_ulum';

// Membuat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8");

// ========== FUNGSI HELPER ==========

// 1. Fungsi untuk menjalankan query
function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// 2. Fungsi untuk mengambil 1 data (associative array)
function fetch($result) {
    return mysqli_fetch_assoc($result);
}

// 3. Fungsi untuk mengambil semua data (array of associative array)
function fetchAll($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// 4. Fungsi untuk menghindari SQL Injection
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// 5. Fungsi redirect
function redirect($url) {
    echo "<script>window.location.href='$url';</script>";
    exit();
}

// 6. Fungsi alert
function alert($message) {
    echo "<script>alert('$message');</script>";
}

// 7. Fungsi cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 8. Fungsi cek role admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// 9. Fungsi cek role panitia
function isPanitia() {
    return isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'panitia');
}

// 10. Fungsi cek akses halaman
function cekAkses($allowed_roles = []) {
    if (!isLoggedIn()) {
        redirect("../public/login.php");
    }
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        alert("Akses ditolak!");
        redirect("dashboard.php");
    }
}

// 11. Fungsi upload gambar (opsional - bisa dipanggil di file lain)
function uploadGambar($file, $target_folder = "berita") {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    
    // Cek ekstensi
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Format gambar tidak didukung!'];
    }
    
    // Cek ukuran (maks 2MB)
    if ($file_size > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Ukuran gambar terlalu besar! Maksimal 2MB'];
    }
    
    // Buat folder jika belum ada
    $upload_dir = "../assets/uploads/$target_folder/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Buat nama file unik
    $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
    $target_path = $upload_dir . $new_filename;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $target_path)) {
        return ['success' => true, 'filename' => "assets/uploads/$target_folder/$new_filename"];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload gambar!'];
    }
}

// 12. Fungsi untuk mendapatkan data pengaturan (opsional)
function getSetting($key) {
    global $conn;
    $query = "SELECT value FROM settings WHERE key = '$key'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['value'];
    }
    return null;
}
?>