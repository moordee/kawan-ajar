-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_kawanajar
CREATE DATABASE IF NOT EXISTS `db_kawanajar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_kawanajar`;

-- Dumping structure for table db_kawanajar.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.cache: ~0 rows (approximately)
DELETE FROM `cache`;

-- Dumping structure for table db_kawanajar.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table db_kawanajar.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table db_kawanajar.guru
CREATE TABLE IF NOT EXISTS `guru` (
  `guru_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kata_sandi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guru',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`guru_id`),
  UNIQUE KEY `guru_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.guru: ~1 rows (approximately)
DELETE FROM `guru`;
INSERT INTO `guru` (`guru_id`, `nama`, `email`, `kata_sandi`, `peran`, `created_at`, `updated_at`, `remember_token`) VALUES
	(1, 'Ibu Guru Demo', 'demo@kawanajar.id', '$2y$12$zatM2jli/RGmbqEp2ogeUeXky3jt62AzGTtQTbeGIxCYQSBG3MQAq', 'guru', '2026-10-29 08:24:39', '2026-10-29 15:56:59', NULL);

-- Dumping structure for table db_kawanajar.guru_kelas_mapel
CREATE TABLE IF NOT EXISTS `guru_kelas_mapel` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `mapel_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guru_kelas_mapel_guru_id_kelas_id_mapel_id_unique` (`guru_id`,`kelas_id`,`mapel_id`),
  KEY `guru_kelas_mapel_kelas_id_foreign` (`kelas_id`),
  KEY `guru_kelas_mapel_mapel_id_foreign` (`mapel_id`),
  CONSTRAINT `guru_kelas_mapel_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE CASCADE,
  CONSTRAINT `guru_kelas_mapel_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE CASCADE,
  CONSTRAINT `guru_kelas_mapel_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`mapel_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.guru_kelas_mapel: ~2 rows (approximately)
DELETE FROM `guru_kelas_mapel`;
INSERT INTO `guru_kelas_mapel` (`id`, `guru_id`, `kelas_id`, `mapel_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, '2026-10-29 15:56:59', '2026-10-29 15:56:59'),
	(2, 1, 1, 2, '2026-10-29 15:57:00', '2026-10-29 15:57:00');

-- Dumping structure for table db_kawanajar.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table db_kawanajar.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table db_kawanajar.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `kelas_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kelas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.kelas: ~0 rows (approximately)
DELETE FROM `kelas`;
INSERT INTO `kelas` (`kelas_id`, `nama_kelas`, `tingkat`, `status_aktif`, `created_at`, `updated_at`) VALUES
	(1, '4A', '4', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41');

-- Dumping structure for table db_kawanajar.mata_pelajaran
CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `mapel_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mapel_id`),
  UNIQUE KEY `mata_pelajaran_kode_mapel_unique` (`kode_mapel`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.mata_pelajaran: ~4 rows (approximately)
DELETE FROM `mata_pelajaran`;
INSERT INTO `mata_pelajaran` (`mapel_id`, `kode_mapel`, `nama_mapel`, `created_at`, `updated_at`) VALUES
	(1, 'MAT4', 'Matematika', '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(2, 'BIN4', 'Bahasa Indonesia', '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(3, 'IPAS4', 'IPAS', '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(4, 'PPKN4', 'Pendidikan Pancasila', '2026-10-29 08:24:41', '2026-10-29 08:24:41');

-- Dumping structure for table db_kawanajar.materi_pelajaran
CREATE TABLE IF NOT EXISTS `materi_pelajaran` (
  `materi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul_materi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_materi` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `lampiran_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guru_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `mapel_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`materi_id`),
  KEY `materi_pelajaran_guru_id_foreign` (`guru_id`),
  KEY `materi_pelajaran_kelas_id_foreign` (`kelas_id`),
  KEY `materi_pelajaran_mapel_id_foreign` (`mapel_id`),
  CONSTRAINT `materi_pelajaran_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE RESTRICT,
  CONSTRAINT `materi_pelajaran_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE RESTRICT,
  CONSTRAINT `materi_pelajaran_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`mapel_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.materi_pelajaran: ~0 rows (approximately)
DELETE FROM `materi_pelajaran`;
INSERT INTO `materi_pelajaran` (`materi_id`, `judul_materi`, `isi_materi`, `lampiran_dokumen`, `tanggal_dibuat`, `guru_id`, `kelas_id`, `mapel_id`, `created_at`, `updated_at`) VALUES
	(1, 'Mengenal Pecahan di Sekitar Kita', 'Pecahan menunjukkan sebagian dari satu benda utuh. Pembilang adalah angka di atas garis pecahan yang menunjukkan jumlah bagian yang kita ambil. Penyebut adalah angka di bawah garis pecahan yang menunjukkan jumlah seluruh bagian yang sama besar. Contoh: satu potong dari empat potong pizza ditulis 1/4.', NULL, '2026-10-29 08:24:41', 1, 1, 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41');

-- Dumping structure for table db_kawanajar.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.migrations: ~4 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(5, '2026_10_29_140626_create_kawanajar_schema', 2),
	(6, '2026_10_29_224613_add_remember_token_to_guru_table', 3);

-- Dumping structure for table db_kawanajar.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table db_kawanajar.pilihan_jawaban
CREATE TABLE IF NOT EXISTS `pilihan_jawaban` (
  `pilihan_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `huruf_opsi` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teks_opsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_kunci_jawaban` tinyint(1) NOT NULL DEFAULT '0',
  `soal_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pilihan_id`),
  UNIQUE KEY `pilihan_jawaban_soal_id_huruf_opsi_unique` (`soal_id`,`huruf_opsi`),
  CONSTRAINT `pilihan_jawaban_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`soal_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.pilihan_jawaban: ~12 rows (approximately)
DELETE FROM `pilihan_jawaban`;
INSERT INTO `pilihan_jawaban` (`pilihan_id`, `huruf_opsi`, `teks_opsi`, `is_kunci_jawaban`, `soal_id`, `created_at`, `updated_at`) VALUES
	(1, 'A', '1/2', 0, 1, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(2, 'B', '1/3', 0, 1, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(3, 'C', '1/4', 1, 1, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(4, 'D', '4/1', 0, 1, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(5, 'A', '3', 0, 2, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(6, 'B', '5', 1, 2, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(7, 'C', '8', 0, 2, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(8, 'D', '2', 0, 2, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(9, 'A', 'Siti', 1, 4, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(10, 'B', 'Ibu', 0, 4, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(11, 'C', 'Ayah', 0, 4, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(12, 'D', 'Kakak', 0, 4, '2026-10-29 08:24:42', '2026-10-29 08:24:42');

-- Dumping structure for table db_kawanajar.sesi_pembuatan_soal_ai
CREATE TABLE IF NOT EXISTS `sesi_pembuatan_soal_ai` (
  `sesi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prompt_instruksi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_soal_diminta` smallint unsigned NOT NULL,
  `jenis_soal_diminta` json NOT NULL,
  `tingkat_kesulitan_diminta` json NOT NULL,
  `tanggal_sesi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guru_id` bigint unsigned NOT NULL,
  `materi_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sesi_id`),
  KEY `sesi_pembuatan_soal_ai_guru_id_foreign` (`guru_id`),
  KEY `sesi_pembuatan_soal_ai_materi_id_foreign` (`materi_id`),
  CONSTRAINT `sesi_pembuatan_soal_ai_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE RESTRICT,
  CONSTRAINT `sesi_pembuatan_soal_ai_materi_id_foreign` FOREIGN KEY (`materi_id`) REFERENCES `materi_pelajaran` (`materi_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.sesi_pembuatan_soal_ai: ~0 rows (approximately)
DELETE FROM `sesi_pembuatan_soal_ai`;

-- Dumping structure for table db_kawanajar.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.sessions: ~5 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('8vVCmFRfBi3qXRsSqAF5TqYlTs9roAqXGEajPjnu', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.144.0 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUmY5a21OelYxcFd6cXVJQXp4ckxQZ0FLR1NYOEV3a0gyeFBCQXdZQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9iYW5rLXNvYWwvYnVhdCI7czo1OiJyb3V0ZSI7czoxNjoiYmFuay1zb2FsLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjNhNTlkYjE2MDQxNGM2NmE3OTlhMTczMGM3YjE4ZWIwMzViNjkyODE2NjBlZjgwM2I1NTAzMmMzNjdmNmI1NjgiO30=', 1793321462),
	('AhtuMBbgVEG7fcEfSxUxxGbxHGFtXIG9scQpUhCX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.22621.6133', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREJvVWExQ3R0b1MxajNuZDdoRTFGb2NYUTBNY3VXYlFzQXZpY1AyQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9tYXN1ayI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1793287575),
	('b0nARwcIgNyNbtAeBfw3wZwJuD93ySaRvdeNJqC9', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.144.0 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVmh5anlzblFLV3B0eDV1dWxIbGhyTmN4MDJnd1VyMlM5VU4zeXFBVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9oYXNpbCI7czo1OiJyb3V0ZSI7czoxMToiaGFzaWwuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiIzYTU5ZGIxNjA0MTRjNjZhNzk5YTE3MzBjN2IxOGViMDM1YjY5MjgxNjYwZWY4MDNiNTUwMzJjMzY3ZjZiNTY4Ijt9', 1793319804),
	('gXBhpEoJWPyQOjvYUtt0C6HqsLj4uibW6BVc4I9y', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.22621.6133', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0hlR0VlNFFVMWdnYVNoeFNVNWlwMVhvQTByWVVPdTBOQUVZR0phaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9tYXN1ayI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1793321403),
	('iwHHktwWgUs3Ms2XEeKrYjh7iYq1JSuUpAVhQpQN', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.143.1 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVjlER0g4V0JXY1hFUDlUdUFlTUFlQ3piMHV0UUtCTXhFblcxdUZ6aCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAxMC9rZWxhcy8xL2VkaXQiO3M6NToicm91dGUiO3M6MTg6InNla29sYWgua2VsYXMuZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImRhOGFhOWNjZTU4MjgyMWZjMTE5ZmFhZWUwMGU4ZjgyMzRiYjdmOTMzMjkyMzgzNTJjNzQ3ZjM5ZWQ1YWEzOTIiO30=', 1793289869);

-- Dumping structure for table db_kawanajar.siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `siswa_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_siswa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`siswa_id`),
  UNIQUE KEY `siswa_nis_unique` (`nis`),
  KEY `siswa_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `siswa_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.siswa: ~5 rows (approximately)
DELETE FROM `siswa`;
INSERT INTO `siswa` (`siswa_id`, `nama_siswa`, `nis`, `kelas_id`, `created_at`, `updated_at`) VALUES
	(1, 'Alya Putri', 'DEMO4001', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(2, 'Bima Pratama', 'DEMO4002', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(3, 'Citra Lestari', 'DEMO4003', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(4, 'Danu Saputra', 'DEMO4004', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(5, 'Eka Safitri', 'DEMO4005', 1, '2026-10-29 08:24:41', '2026-10-29 08:24:41');

-- Dumping structure for table db_kawanajar.siswa_ujian_besar
CREATE TABLE IF NOT EXISTS `siswa_ujian_besar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `ujian_besar_id` bigint unsigned NOT NULL,
  `skor` decimal(7,2) DEFAULT NULL,
  `tanggal_dikerjakan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_ujian_besar_siswa_id_ujian_besar_id_unique` (`siswa_id`,`ujian_besar_id`),
  KEY `siswa_ujian_besar_ujian_besar_id_foreign` (`ujian_besar_id`),
  CONSTRAINT `siswa_ujian_besar_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_ujian_besar_ujian_besar_id_foreign` FOREIGN KEY (`ujian_besar_id`) REFERENCES `ujian_besar` (`ujian_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.siswa_ujian_besar: ~0 rows (approximately)
DELETE FROM `siswa_ujian_besar`;

-- Dumping structure for table db_kawanajar.siswa_ulangan
CREATE TABLE IF NOT EXISTS `siswa_ulangan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `ulangan_id` bigint unsigned NOT NULL,
  `skor` decimal(7,2) DEFAULT NULL,
  `tanggal_dikerjakan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_ulangan_siswa_id_ulangan_id_unique` (`siswa_id`,`ulangan_id`),
  KEY `siswa_ulangan_ulangan_id_foreign` (`ulangan_id`),
  CONSTRAINT `siswa_ulangan_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_ulangan_ulangan_id_foreign` FOREIGN KEY (`ulangan_id`) REFERENCES `ulangan` (`ulangan_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.siswa_ulangan: ~0 rows (approximately)
DELETE FROM `siswa_ulangan`;

-- Dumping structure for table db_kawanajar.soal
CREATE TABLE IF NOT EXISTS `soal` (
  `soal_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teks_soal` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_soal` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat_kesulitan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bab_materi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sumber_soal` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prompt_yang_digunakan` text COLLATE utf8mb4_unicode_ci,
  `mapel_id` bigint unsigned NOT NULL,
  `sesi_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`soal_id`),
  KEY `soal_mapel_id_foreign` (`mapel_id`),
  KEY `soal_sesi_id_foreign` (`sesi_id`),
  CONSTRAINT `soal_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`mapel_id`) ON DELETE RESTRICT,
  CONSTRAINT `soal_sesi_id_foreign` FOREIGN KEY (`sesi_id`) REFERENCES `sesi_pembuatan_soal_ai` (`sesi_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.soal: ~4 rows (approximately)
DELETE FROM `soal`;
INSERT INTO `soal` (`soal_id`, `teks_soal`, `jenis_soal`, `tingkat_kesulitan`, `bab_materi`, `sumber_soal`, `prompt_yang_digunakan`, `mapel_id`, `sesi_id`, `created_at`, `updated_at`) VALUES
	(1, 'Sebuah kue dipotong menjadi 4 bagian sama besar. Jika Rani mengambil 1 bagian, pecahan yang menunjukkan bagian kue yang diambil Rani adalah ....', 'Pilihan Ganda', 'Mudah', 'Pecahan sederhana', 'Dibuat Manual', NULL, 1, NULL, '2026-10-29 08:24:41', '2026-10-29 08:24:41'),
	(2, 'Pada pecahan 3/5, angka yang menunjukkan banyaknya seluruh bagian yang sama besar adalah ....', 'Pilihan Ganda', 'Mudah', 'Pembilang dan penyebut', 'Dibuat Manual', NULL, 1, NULL, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(3, 'Ibu memotong semangka menjadi 8 bagian sama besar. Adik makan 2 bagian. Tuliskan pecahan bagian semangka yang dimakan adik.', 'Esai', 'Sedang', 'Pecahan dalam kehidupan sehari-hari', 'Dibuat Manual', NULL, 1, NULL, '2026-10-29 08:24:42', '2026-10-29 08:24:42'),
	(4, 'Bacalah kalimat berikut: "Setiap pagi, Siti menyiram bunga di halaman rumah." Siapakah yang menyiram bunga?', 'Pilihan Ganda', 'Mudah', 'Menemukan informasi dalam kalimat', 'Dibuat Manual', NULL, 2, NULL, '2026-10-29 08:24:42', '2026-10-29 08:24:42');

-- Dumping structure for table db_kawanajar.ujian_besar
CREATE TABLE IF NOT EXISTS `ujian_besar` (
  `ujian_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul_ujian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_ujian` smallint unsigned NOT NULL,
  `jumlah_soal` smallint unsigned NOT NULL,
  `acak_soal` tinyint(1) NOT NULL DEFAULT '0',
  `acak_pilihan` tinyint(1) NOT NULL DEFAULT '0',
  `bobot_nilai_per_bab` json DEFAULT NULL,
  `bobot_nilai_per_tingkat_kesulitan` json DEFAULT NULL,
  `total_bobot` decimal(7,2) NOT NULL DEFAULT '100.00',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guru_id` bigint unsigned NOT NULL,
  `mapel_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ujian_id`),
  KEY `ujian_besar_guru_id_foreign` (`guru_id`),
  KEY `ujian_besar_mapel_id_foreign` (`mapel_id`),
  CONSTRAINT `ujian_besar_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE RESTRICT,
  CONSTRAINT `ujian_besar_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`mapel_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.ujian_besar: ~1 rows (approximately)
DELETE FROM `ujian_besar`;
INSERT INTO `ujian_besar` (`ujian_id`, `judul_ujian`, `semester`, `waktu_ujian`, `jumlah_soal`, `acak_soal`, `acak_pilihan`, `bobot_nilai_per_bab`, `bobot_nilai_per_tingkat_kesulitan`, `total_bobot`, `tanggal_dibuat`, `guru_id`, `mapel_id`, `created_at`, `updated_at`) VALUES
	(1, 'Latihan Tengah Semester — Matematika', 'Semester 1', 60, 3, 0, 0, '{"Pecahan sederhana": 50, "Pembilang dan penyebut": 50}', '{"Mudah": 2, "Sedang": 1}', 100.00, '2026-10-29 08:24:44', 1, 1, '2026-10-29 08:24:44', '2026-10-29 08:24:44');

-- Dumping structure for table db_kawanajar.ujian_besar_soal
CREATE TABLE IF NOT EXISTS `ujian_besar_soal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ujian_besar_id` bigint unsigned NOT NULL,
  `soal_id` bigint unsigned NOT NULL,
  `nomor_urut` smallint unsigned NOT NULL,
  `bobot_nilai` decimal(7,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ujian_besar_soal_ujian_besar_id_soal_id_unique` (`ujian_besar_id`,`soal_id`),
  UNIQUE KEY `ujian_besar_soal_ujian_besar_id_nomor_urut_unique` (`ujian_besar_id`,`nomor_urut`),
  KEY `ujian_besar_soal_soal_id_foreign` (`soal_id`),
  CONSTRAINT `ujian_besar_soal_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`soal_id`) ON DELETE CASCADE,
  CONSTRAINT `ujian_besar_soal_ujian_besar_id_foreign` FOREIGN KEY (`ujian_besar_id`) REFERENCES `ujian_besar` (`ujian_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.ujian_besar_soal: ~3 rows (approximately)
DELETE FROM `ujian_besar_soal`;
INSERT INTO `ujian_besar_soal` (`id`, `ujian_besar_id`, `soal_id`, `nomor_urut`, `bobot_nilai`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:02'),
	(2, 1, 2, 2, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:02'),
	(3, 1, 3, 3, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:02');

-- Dumping structure for table db_kawanajar.ulangan
CREATE TABLE IF NOT EXISTS `ulangan` (
  `ulangan_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul_ulangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_soal_diizinkan` json NOT NULL,
  `acak_soal` tinyint(1) NOT NULL DEFAULT '0',
  `acak_opsi` tinyint(1) NOT NULL DEFAULT '0',
  `tampilkan_hasil` tinyint(1) NOT NULL DEFAULT '1',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guru_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `mapel_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ulangan_id`),
  KEY `ulangan_guru_id_foreign` (`guru_id`),
  KEY `ulangan_kelas_id_foreign` (`kelas_id`),
  KEY `ulangan_mapel_id_foreign` (`mapel_id`),
  CONSTRAINT `ulangan_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`) ON DELETE RESTRICT,
  CONSTRAINT `ulangan_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE RESTRICT,
  CONSTRAINT `ulangan_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`mapel_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.ulangan: ~2 rows (approximately)
DELETE FROM `ulangan`;
INSERT INTO `ulangan` (`ulangan_id`, `judul_ulangan`, `jenis_soal_diizinkan`, `acak_soal`, `acak_opsi`, `tampilkan_hasil`, `tanggal_dibuat`, `guru_id`, `kelas_id`, `mapel_id`, `created_at`, `updated_at`) VALUES
	(1, 'Latihan Pecahan — Kelas 4A', '["Pilihan Ganda", "Esai"]', 0, 0, 1, '2026-10-29 08:24:42', 1, 1, 1, '2026-10-29 08:24:42', '2026-10-29 08:24:42');

-- Dumping structure for table db_kawanajar.ulangan_soal
CREATE TABLE IF NOT EXISTS `ulangan_soal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ulangan_id` bigint unsigned NOT NULL,
  `soal_id` bigint unsigned NOT NULL,
  `nomor_urut` smallint unsigned NOT NULL,
  `bobot_nilai` decimal(7,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ulangan_soal_ulangan_id_soal_id_unique` (`ulangan_id`,`soal_id`),
  UNIQUE KEY `ulangan_soal_ulangan_id_nomor_urut_unique` (`ulangan_id`,`nomor_urut`),
  KEY `ulangan_soal_soal_id_foreign` (`soal_id`),
  CONSTRAINT `ulangan_soal_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`soal_id`) ON DELETE CASCADE,
  CONSTRAINT `ulangan_soal_ulangan_id_foreign` FOREIGN KEY (`ulangan_id`) REFERENCES `ulangan` (`ulangan_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.ulangan_soal: ~3 rows (approximately)
DELETE FROM `ulangan_soal`;
INSERT INTO `ulangan_soal` (`id`, `ulangan_id`, `soal_id`, `nomor_urut`, `bobot_nilai`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:01'),
	(2, 1, 2, 2, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:01'),
	(3, 1, 3, 3, 1.00, '2026-10-29 08:24:44', '2026-10-29 15:57:01');

-- Dumping structure for table db_kawanajar.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kawanajar.users: ~0 rows (approximately)
DELETE FROM `users`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
