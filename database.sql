-- ============================================
-- Database: perpustakaan
-- Tema: Manajemen Buku Perpustakaan
-- ============================================

CREATE DATABASE IF NOT EXISTS perpustakaan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE perpustakaan;

-- ============================================
-- Tabel Users (Login)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100)  NOT NULL,
  username   VARCHAR(50)   NOT NULL UNIQUE,
  password   VARCHAR(255)  NOT NULL,
  role       ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabel Buku
-- ============================================
CREATE TABLE IF NOT EXISTS buku (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(255) NOT NULL,
  pengarang    VARCHAR(150) NOT NULL,
  penerbit     VARCHAR(150) NOT NULL,
  tahun_terbit YEAR         NOT NULL,
  kategori     ENUM('Fiksi','Non-Fiksi','Sains','Teknologi','Sejarah','Biografi','Lainnya') NOT NULL DEFAULT 'Lainnya',
  stok         INT          NOT NULL DEFAULT 0,
  created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Data Awal Buku (Seed)
-- ============================================
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES
  ('Laskar Pelangi',           'Andrea Hirata',        'Bentang Pustaka',   2005, 'Fiksi',     12),
  ('Bumi Manusia',             'Pramoedya Ananta Toer','Hasta Mitra',        1980, 'Fiksi',      8),
  ('Sapiens',                  'Yuval Noah Harari',    'KPG',                2011, 'Sejarah',    5),
  ('Clean Code',               'Robert C. Martin',     'Prentice Hall',      2008, 'Teknologi',  7),
  ('Atomic Habits',            'James Clear',          'Avery',              2018, 'Non-Fiksi', 10),
  ('A Brief History of Time',  'Stephen Hawking',      'Bantam Books',       1988, 'Sains',      4),
  ('Steve Jobs',               'Walter Isaacson',      'Simon & Schuster',   2011, 'Biografi',   6);

-- ============================================
-- Data Awal User (Seed)
-- PENTING: Jalankan generate_hash.php dulu di browser untuk mendapat
-- hash yang valid, lalu ganti nilai password di bawah ini.
-- Hash di bawah sudah valid untuk PHP password_hash() dengan PASSWORD_BCRYPT:
--   admin    -> admin123
--   petugas  -> petugas123
-- ============================================
INSERT INTO users (nama, username, password, role) VALUES
('Administrator',
 'admin',
 '$2y$12$zLUJW0HTrnYjyhgmnBHqGOV8jCpKFkYFpTdLy0eyJS9bed9LWZIyO',
 'admin'),

('Petugas Perpustakaan',
 'petugas',
 '$2y$12$33x3xAJHZH/Ei85E/ACiGumulpNSvqt7o0Owo0WF1w7sl6o546AXW',
 'petugas');