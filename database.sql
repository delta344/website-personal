-- Buat database
CREATE DATABASE IF NOT EXISTS db_mts_bahrul_ulum;
USE db_mts_bahrul_ulum;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'panitia', 'siswa') DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel profil
CREATE TABLE profil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe ENUM('visi', 'misi', 'sejarah', 'fasilitas', 'struktur') NOT NULL,
    konten TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel akademik (kurikulum & kalender)
CREATE TABLE akademik (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe ENUM('kurikulum', 'kalender') NOT NULL,
    judul VARCHAR(255),
    isi TEXT,
    file_upload VARCHAR(255),
    tahun_ajaran VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel tenaga pengajar
CREATE TABLE pengajar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(50),
    jabatan VARCHAR(100),
    mapel VARCHAR(100),
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kesiswaan (eskul, osis, prestasi)
CREATE TABLE kesiswaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe ENUM('eskul', 'osis', 'prestasi') NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),
    tanggal DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel PPDB
CREATE TABLE ppdb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_pendaftaran VARCHAR(20) UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    nisn VARCHAR(20),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    alamat TEXT,
    no_hp VARCHAR(15),
    asal_sekolah VARCHAR(100),
    status ENUM('pending', 'verifikasi', 'lulus', 'tidak_lulus') DEFAULT 'pending',
    tgl_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel berita
CREATE TABLE berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT,
    kategori ENUM('pengumuman', 'artikel', 'kegiatan') DEFAULT 'artikel',
    foto VARCHAR(255),
    views INT DEFAULT 0,
    tgl_post TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kontak
CREATE TABLE kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alamat TEXT,
    gmaps_link TEXT,
    wa_number VARCHAR(20),
    email VARCHAR(100),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    youtube VARCHAR(255)
);

-- Insert data awal
INSERT INTO users (username, password, role) VALUES 
('admin', MD5('admin123'), 'admin'),
('panitia1', MD5('panitia123'), 'panitia'),
('siswa1', MD5('siswa123'), 'siswa');

INSERT INTO kontak (alamat, gmaps_link, wa_number, email, facebook, instagram) VALUES
('Telage Bagek, Ketapang Raya, Keruak, Lombok Timur, NTB', 'https://www.google.com/maps/embed?pb=...', '087753235268', 'info@mtsbahrululum.sch.id', 'mtsbahrululum', '@mtsbahrululum');

INSERT INTO profil (tipe, konten) VALUES
('visi', 'Mewujudkan generasi yang beriman, bertaqwa, berilmu, dan berakhlak mulia'),
('misi', '1. Melaksanakan pembelajaran yang efektif\n2. Mengembangkan potensi siswa\n3. Menanamkan nilai-nilai keislaman'),
('sejarah', 'MTs Bahrul Ulum NW Telage Bagek didirikan pada tahun...');