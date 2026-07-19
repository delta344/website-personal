<?php
// config/upload.php
function uploadImage($file, $folder, $max_size = 2 * 1024 * 1024) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload gagal!'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar! Maksimal 2MB'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak didukung!'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return ['success' => false, 'message' => 'Ekstensi file tidak didukung!'];
    }
    
    $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
    $target_path = "../assets/uploads/$folder/" . $new_filename;
    
    if (!is_dir("../assets/uploads/$folder")) {
        mkdir("../assets/uploads/$folder", 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => "assets/uploads/$folder/" . $new_filename];
    }
    
    return ['success' => false, 'message' => 'Gagal menyimpan file!'];
}
?>