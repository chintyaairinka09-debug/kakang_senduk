-- Database Aplikasi Voting Kakang Senduk Ponorogo
-- Import file ini melalui phpMyAdmin. Data dan pembayaran di bawah hanya untuk DEMO.
CREATE DATABASE IF NOT EXISTS db_voting_kakang_senduk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_voting_kakang_senduk;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS voting;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS kontestan;
DROP TABLE IF EXISTS periode_voting;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  no_hp VARCHAR(20) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','pemilih') NOT NULL DEFAULT 'pemilih',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE periode_voting (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_periode VARCHAR(100) NOT NULL,
  tanggal_mulai DATETIME NOT NULL,
  tanggal_selesai DATETIME NOT NULL,
  status ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Nonaktif',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kontestan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nomor_peserta VARCHAR(10) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  kategori ENUM('Kakang','Senduk') NOT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  instagram_url VARCHAR(255) DEFAULT NULL,
  asal_instansi VARCHAR(150) DEFAULT NULL,
  tempat_lahir VARCHAR(100) DEFAULT NULL,
  tanggal_lahir DATE DEFAULT NULL,
  visi TEXT,
  misi TEXT,
  prestasi TEXT,
  deskripsi TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nomor_kategori (nomor_peserta, kategori)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  periode_id INT UNSIGNED NOT NULL,
  amount INT UNSIGNED NOT NULL DEFAULT 3000,
  provider VARCHAR(50) NOT NULL DEFAULT 'MOCK_GATEWAY',
  reference VARCHAR(60) NOT NULL UNIQUE,
  status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  paid_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payment_user_period (user_id, periode_id),
  CONSTRAINT fk_payment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_payment_period FOREIGN KEY (periode_id) REFERENCES periode_voting(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE voting (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  kontestan_id INT UNSIGNED NOT NULL,
  periode_id INT UNSIGNED NOT NULL,
  kategori ENUM('Kakang','Senduk') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_one_vote_per_category (user_id, periode_id, kategori),
  CONSTRAINT fk_voting_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_voting_candidate FOREIGN KEY (kontestan_id) REFERENCES kontestan(id) ON DELETE RESTRICT,
  CONSTRAINT fk_voting_period FOREIGN KEY (periode_id) REFERENCES periode_voting(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Akun demo: admin@ponorogo.local / admin123
INSERT INTO users (nama,email,no_hp,password,role) VALUES
('Administrator','admin@ponorogo.local','081234567890','$2y$12$41UaV1eRwM5hfIVhXVGZsODXyRlQuvM1JDrXxwweoyGRKtQyg85KK','admin'),
('Pemilih Demo','pemilih@ponorogo.local','081234567891','$2y$12$aU9DTdWnbT2pAn/3KL5uAeNrs4uE5PqNJGz.P5htG/BjWSEeVQ0Wm','pemilih');

INSERT INTO periode_voting (nama_periode,tanggal_mulai,tanggal_selesai,status) VALUES
('Pemilihan Kakang Senduk Ponorogo 2026','2026-11-01 00:00:00','2026-12-31 23:59:59','Aktif');

-- Foto belum diunduh; foto akan memakai placeholder dan tautan Instagram tetap tersedia.
INSERT INTO kontestan (nomor_peserta,nama,kategori,instagram_url) VALUES
('01','Fahrizal','Kakang','https://www.instagram.com/p/DfXEQnaFF2s/?psln=MW11c3FqYzNsdWl5ZA=='),
('02','Callysta','Senduk','https://www.instagram.com/p/DfXDvZyFCaC/?dlrf=MTlhcXNicnphYmRzaw=='),
('03','Alzam','Kakang','https://www.instagram.com/p/DfXE6tNFNT_/?dlrf=Y2lrM3hoNHo0NTYx'),
('04','Renata','Senduk','https://www.instagram.com/p/DfXEfAElK0b/?xtok=MTZ4NTU5NTA2dnF6Nw=='),
('05','Rizal','Kakang','https://www.instagram.com/p/DfXHCVXFOdJ/?cplk=MXB2YWt0OWhlbWU5bQ=='),
('06','Mozza','Senduk','https://www.instagram.com/p/DfXFWZplDWA/?obrf=ZHF2czQwNHkxeHZ5'),
('07','Efeel','Kakang','https://www.instagram.com/p/DfXIXUElFLF/?exln=Y2I3YWIwZXpycnps'),
('08','Sendy','Senduk','https://www.instagram.com/p/DfXHoQilMXq/?rpxt=MWswa2p1c2x0NW1scA=='),
('09','Viko','Kakang','https://www.instagram.com/p/DfXJVDBEp1U/?obrf=N3M3N2xkc3RjdjF1'),
('10','Nabila','Senduk','https://www.instagram.com/p/DfXIt_3FMyN/?dlrf=MXU1NXVoNGIwaTcycg=='),
('11','Naufal','Kakang','https://www.instagram.com/p/DfXO7-zks3U/?mdxt=YjQxcHYyYnc4MjMx'),
('12','Khalila','Senduk','https://www.instagram.com/p/DfXNm_eEskQ/?psln=MWc4dDE2a2J3N2gwZQ=='),
('13','Moreno','Kakang','https://www.instagram.com/p/DfXRE5DEogm/?psln=YjM4OXNxZmV4cG5w'),
('14','Olivia','Senduk','https://www.instagram.com/p/DfXQK7pkr3P/?obrf=MXdoMXQ5dzA3cmcxYw=='),
('15','Harjuno','Kakang','https://www.instagram.com/p/DfXVGsKktBE/?srtk=Z3B4a3VmbHR2MjVq'),
('16','Nadia','Senduk','https://www.instagram.com/p/DfXTsDNEnj8/?xtok=bThmeTF6YzFmdzJn'),
('17','Kenang','Kakang','https://www.instagram.com/p/DfXV0NaEk7Y/?vrfl=YmNpZXNmMjM0eTgz'),
('18','Kayla','Senduk','https://www.instagram.com/p/DfXVXKdEpG0/?cplk=MWxpMzVsdXBsdGx0aQ=='),
('19','Althaf','Kakang','https://www.instagram.com/p/DfXW2ALkq5Z/?obrf=MXUwaW95dTlhcjBtOQ=='),
('20','Fahriya','Senduk','https://www.instagram.com/p/DfXWNbKEs4X/?mdxt=MTB5dzJ1cHJhc2xieQ==');
