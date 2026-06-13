-- ============================================
-- Database: perpustakaan
-- Tema: Manajemen Buku Perpustakaan
-- ============================================

CREATE DATABASE IF NOT EXISTS perpustakaan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE perpustakaan;

-- Tabel Buku
CREATE TABLE IF NOT EXISTS buku (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  judul       VARCHAR(255) NOT NULL,
  pengarang   VARCHAR(150) NOT NULL,
  penerbit    VARCHAR(150) NOT NULL,
  tahun_terbit YEAR         NOT NULL,
  kategori    ENUM('Fiksi','Non-Fiksi','Sains','Teknologi','Sejarah','Biografi','Lainnya') NOT NULL DEFAULT 'Lainnya',
  stok        INT          NOT NULL DEFAULT 0,
  created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data Awal (Seed)
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES
  ('Laskar Pelangi',           'Andrea Hirata',       'Bentang Pustaka',   2005, 'Fiksi',      12),
  ('Bumi Manusia',             'Pramoedya Ananta Toer','Hasta Mitra',       1980, 'Fiksi',       8),
  ('Sapiens',                  'Yuval Noah Harari',   'KPG',               2011, 'Sejarah',     5),
  ('Clean Code',               'Robert C. Martin',    'Prentice Hall',     2008, 'Teknologi',   7),
  ('Atomic Habits',            'James Clear',         'Avery',             2018, 'Non-Fiksi',  10),
  ('A Brief History of Time',  'Stephen Hawking',     'Bantam Books',      1988, 'Sains',       4),
  ('Steve Jobs',               'Walter Isaacson',     'Simon & Schuster',  2011, 'Biografi',    6);
