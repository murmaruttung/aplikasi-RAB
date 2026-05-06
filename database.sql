-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 05, 2026 at 06:06 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u1643084_humasapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'Login berhasil', '192.168.1.1', 'Mozilla/5.0 Chrome/120', '2026-05-05 11:04:22'),
(2, 2, 'CREATE_PENUGASAN', 'Membuat penugasan monitoring workshop', '192.168.1.2', 'Mozilla/5.0 Firefox/120', '2026-05-05 11:04:22'),
(3, 3, 'SUBMIT_LAPORAN', 'Submit laporan sekolah SMA N 1', '192.168.1.3', 'MobileApp/1.0', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `api_key_hash` varchar(255) NOT NULL,
  `app_name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `request_limit` int(11) DEFAULT 0,
  `request_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`id`, `user_id`, `api_key_hash`, `app_name`, `description`, `request_limit`, `request_count`, `is_active`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0', 'Mobile App Monitoring', 'Untuk aplikasi monitoring mobile', 10000, 1200, 1, NULL, '2026-12-31 16:59:59', '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 2, 'b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0a1', 'Web Dashboard', 'Untuk dashboard pengawas', 5000, 450, 1, NULL, '2026-12-31 16:59:59', '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 3, 'c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0a1b2', 'Bot Laporan', 'Untuk integrasi bot Telegram', 1000, 100, 1, NULL, '2026-06-30 16:59:59', '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `api_key_domains`
--

CREATE TABLE `api_key_domains` (
  `id` int(10) UNSIGNED NOT NULL,
  `api_key_id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_key_domains`
--

INSERT INTO `api_key_domains` (`id`, `api_key_id`, `domain`, `is_active`, `created_at`) VALUES
(1, 1, 'app.dinas.go.id', 1, '2026-05-05 11:04:22'),
(2, 1, 'api.dinas.go.id', 1, '2026-05-05 11:04:22'),
(3, 2, 'dashboard.dinas.go.id', 1, '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `api_resources`
--

CREATE TABLE `api_resources` (
  `table_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `description` varchar(500) DEFAULT NULL,
  `allowed_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array role yang diizinkan' CHECK (json_valid(`allowed_roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_resources`
--

INSERT INTO `api_resources` (`table_name`, `is_active`, `description`, `allowed_roles`) VALUES
('kegiatan', 1, 'CRUD kegiatan', '[\"admin\",\"pengawas\",\"pelaksana\"]'),
('penugasan', 1, 'CRUD penugasan', '[\"admin\",\"pengawas\"]'),
('sekolah', 1, 'Read-only sekolah untuk pelaksana', '[\"admin\",\"pengawas\",\"pelaksana\"]');

-- --------------------------------------------------------

--
-- Table structure for table `domain_whitelist`
--

CREATE TABLE `domain_whitelist` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `added_by` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `domain_whitelist`
--

INSERT INTO `domain_whitelist` (`id`, `domain`, `description`, `added_by`, `is_active`, `created_at`) VALUES
(1, '*.dinas.go.id', 'Subdomain resmi dinas', 1, 1, '2026-05-05 11:04:22'),
(2, 'localhost', 'Development', 1, 1, '2026-05-05 11:04:22'),
(3, '*.sekolah.sch.id', 'Domain sekolah binaan', 1, 1, '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `jenis_kegiatan` varchar(50) NOT NULL,
  `status` enum('draft','aktif','selesai','dibatalkan') NOT NULL DEFAULT 'draft',
  `total_anggaran` decimal(15,2) DEFAULT NULL,
  `tor_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `laporan_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `nama_kegiatan`, `jenis_kegiatan`, `status`, `total_anggaran`, `tor_id`, `proposal_id`, `laporan_id`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Workshop Kurikulum Merdeka', 'Workshop', 'selesai', 25000000.00, 1, 1, 1, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 'Bimtek Teaching Factory', 'Bimtek', '', 15000000.00, 2, 2, 2, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 'Sosialisasi Dapodik 2026', 'Sosialisasi', 'draft', 8000000.00, 3, 3, 3, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_jadwal`
--

CREATE TABLE `kegiatan_jadwal` (
  `id` int(10) UNSIGNED NOT NULL,
  `kegiatan_id` int(10) UNSIGNED NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tahapan` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_jadwal`
--

INSERT INTO `kegiatan_jadwal` (`id`, `kegiatan_id`, `sort_order`, `tahapan`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Persiapan', '2026-06-01', '2026-06-09', 'Penyusunan materi dan undangan', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 1, 2, 'Pelaksanaan', '2026-06-10', '2026-06-12', 'Workshop 3 hari', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 1, 3, 'Evaluasi', '2026-06-13', '2026-06-15', 'Penyusunan laporan', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(4, 2, 1, 'Persiapan', '2026-07-01', '2026-07-14', 'Survey sekolah sasaran', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(5, 2, 2, 'Pelaksanaan', '2026-07-15', '2026-07-16', 'Bimtek 2 hari', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(6, 3, 1, 'Sosialisasi', '2026-08-01', '2026-08-01', 'Satu hari penuh', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_laporan`
--

CREATE TABLE `kegiatan_laporan` (
  `id` int(10) UNSIGNED NOT NULL,
  `lpj_pengantar` text DEFAULT NULL,
  `lpj_nama_kegiatan` text DEFAULT NULL,
  `lpj_landasan` text DEFAULT NULL,
  `lpj_oucome` text DEFAULT NULL,
  `lpj_tujuan` text DEFAULT NULL,
  `lpj_output` text DEFAULT NULL,
  `lpj_mutu_akademik` text DEFAULT NULL,
  `lpj_rencana_strategis` text DEFAULT NULL,
  `lpj_sasaran` text DEFAULT NULL,
  `lpj_bentuk_desain` text DEFAULT NULL,
  `lpj_waktu_tempat` text DEFAULT NULL,
  `lpj_panitia_kerja` text DEFAULT NULL,
  `lpj_metode_pelaksanaan` text DEFAULT NULL,
  `lpj_sumber_dana` text DEFAULT NULL,
  `lpj_kronologi` text DEFAULT NULL,
  `lpj_penutup` text DEFAULT NULL,
  `lpj_tanda_tangan` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_laporan`
--

INSERT INTO `kegiatan_laporan` (`id`, `lpj_pengantar`, `lpj_nama_kegiatan`, `lpj_landasan`, `lpj_oucome`, `lpj_tujuan`, `lpj_output`, `lpj_mutu_akademik`, `lpj_rencana_strategis`, `lpj_sasaran`, `lpj_bentuk_desain`, `lpj_waktu_tempat`, `lpj_panitia_kerja`, `lpj_metode_pelaksanaan`, `lpj_sumber_dana`, `lpj_kronologi`, `lpj_penutup`, `lpj_tanda_tangan`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Workshop Kurikulum Merdeka', NULL, NULL, 'Meningkatkan kompetensi guru', '50 guru tersertifikasi', NULL, NULL, '50 guru SMA', NULL, '10-12 Juni 2026, Aula Dinas Pendidikan', NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, NULL, 'Bimtek Teaching Factory', NULL, NULL, 'Meningkatkan keterampilan siswa', '10 unit teaching factory', NULL, NULL, '10 SMK', NULL, '15-16 Juli 2026, Hotel Grand Mulya', NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, NULL, 'Sosialisasi Dapodik 2026', NULL, NULL, 'Pemutakhiran data', 'Data terverifikasi', NULL, NULL, 'Operator sekolah', NULL, '1 Agustus 2026, Ruang Rapat Dinas', NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_proposal`
--

CREATE TABLE `kegiatan_proposal` (
  `id` int(10) UNSIGNED NOT NULL,
  `pro_pengantar` text DEFAULT NULL,
  `pro_nama_kegiatan` text DEFAULT NULL,
  `pro_landasan` text DEFAULT NULL,
  `pro_outcome` text DEFAULT NULL,
  `pro_tujuan` text DEFAULT NULL,
  `pro_output` text DEFAULT NULL,
  `pro_mutu_akademik` text DEFAULT NULL,
  `pro_rencana_strategis` text DEFAULT NULL,
  `pro_sasaran` text DEFAULT NULL,
  `pro_bentuk_desain` text DEFAULT NULL,
  `pro_waktu_tempat` text DEFAULT NULL,
  `pro_panitia_kerja` text DEFAULT NULL,
  `pro_metode_pelaksanaan` text DEFAULT NULL,
  `pro_sumber_dana` text DEFAULT NULL,
  `pro_penutup` text DEFAULT NULL,
  `pro_tanda_tangan` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_proposal`
--

INSERT INTO `kegiatan_proposal` (`id`, `pro_pengantar`, `pro_nama_kegiatan`, `pro_landasan`, `pro_outcome`, `pro_tujuan`, `pro_output`, `pro_mutu_akademik`, `pro_rencana_strategis`, `pro_sasaran`, `pro_bentuk_desain`, `pro_waktu_tempat`, `pro_panitia_kerja`, `pro_metode_pelaksanaan`, `pro_sumber_dana`, `pro_penutup`, `pro_tanda_tangan`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Puji syukur kehadirat Tuhan Yang Maha Esa...', 'Workshop Kurikulum Merdeka', 'Permendikbud No. 56 Tahun 2022', 'Guru menerapkan Kurikulum Merdeka', 'Meningkatkan kompetensi guru', NULL, NULL, NULL, '50 guru SMA se-Kabupaten', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 'Dalam rangka peningkatan mutu SMK...', 'Bimtek Teaching Factory', 'Inpres No. 9 Tahun 2016', 'SMK menerapkan Teaching Factory', 'Meningkatkan keterampilan siswa', NULL, NULL, NULL, '10 SMK se-Kabupaten', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 'Menindaklanjuti kebijakan pusat...', 'Sosialisasi Dapodik 2026', 'Surat Edaran Kemendikbud', 'Operator mahir Dapodik', 'Pemutakhiran data pokok pendidikan', NULL, NULL, NULL, 'Operator sekolah se-Kabupaten', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_rab_item`
--

CREATE TABLE `kegiatan_rab_item` (
  `id` int(10) UNSIGNED NOT NULL,
  `kegiatan_id` int(10) UNSIGNED NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `kode_akun` varchar(50) DEFAULT NULL,
  `nama_akun` varchar(100) DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `volume` int(11) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_rab_item`
--

INSERT INTO `kegiatan_rab_item` (`id`, `kegiatan_id`, `sort_order`, `kode_akun`, `nama_akun`, `uraian`, `volume`, `satuan`, `harga_satuan`, `jumlah`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '5.1.1', 'Belanja ATK', 'Kertas HVS A4 70gr', 10, 'rim', 50000.00, 500000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 1, 2, '5.1.2', 'Belanja Konsumsi', 'Snack peserta', 50, 'box', 30000.00, 1500000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 1, 3, '5.1.3', 'Belanja Narasumber', 'Honor narasumber', 3, 'hari', 1500000.00, 4500000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(4, 2, 1, '5.2.1', 'Belanja ATK', 'Spanduk kegiatan', 2, 'buah', 150000.00, 300000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(5, 2, 2, '5.2.2', 'Belanja Konsumsi', 'Makan siang', 40, 'box', 45000.00, 1800000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(6, 3, 1, '5.3.1', 'Belanja ATK', 'Cetak modul', 30, 'eks', 25000.00, 750000.00, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_tor`
--

CREATE TABLE `kegiatan_tor` (
  `id` int(10) UNSIGNED NOT NULL,
  `lpk_koding` varchar(50) DEFAULT NULL,
  `unit_pelaksana` varchar(100) DEFAULT NULL,
  `lpk_penanggung_jawab` varchar(100) DEFAULT NULL,
  `lpk_nama_kegiatan` varchar(200) DEFAULT NULL,
  `lpk_nominal_anggaran` decimal(15,2) DEFAULT NULL,
  `lpk_sumber_dana` varchar(100) DEFAULT NULL,
  `lpk_narasumber_panitia` text DEFAULT NULL,
  `lpk_jumlah_panitia` varchar(50) DEFAULT NULL,
  `lpk_tempat` varchar(200) DEFAULT NULL,
  `lpk_waktu` varchar(200) DEFAULT NULL,
  `lpk_tanda_tangan` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_tor`
--

INSERT INTO `kegiatan_tor` (`id`, `lpk_koding`, `unit_pelaksana`, `lpk_penanggung_jawab`, `lpk_nama_kegiatan`, `lpk_nominal_anggaran`, `lpk_sumber_dana`, `lpk_narasumber_panitia`, `lpk_jumlah_panitia`, `lpk_tempat`, `lpk_waktu`, `lpk_tanda_tangan`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'KP-2026-001', 'Bidang Pembinaan SMA', 'Dr. Ahmad Fauzi, M.Pd.', 'Workshop Kurikulum Merdeka', 25000000.00, 'APBD', 'Narasumber: Prof. Dr. Siti Aminah, M.Pd.\nPanitia: 10 orang', '12', 'Aula Dinas Pendidikan', '10-12 Juni 2026', NULL, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 'KP-2026-002', 'Bidang Pembinaan SMK', 'Budi Santoso, S.Pd.', 'Bimtek Teaching Factory', 15000000.00, 'APBD', 'Narasumber: Dr. Hadi Prasetyo\nPanitia: 8 orang', '10', 'Hotel Grand Mulya', '15-16 Juli 2026', NULL, 2, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 'KP-2026-003', 'Bidang Perencanaan', 'Ani Rahmawati, S.Kom.', 'Sosialisasi Dapodik 2026', 8000000.00, 'APBN', 'Narasumber: Tim Dapodik Pusat\nPanitia: 5 orang', '7', 'Ruang Rapat Dinas Pendidikan', '1 Agustus 2026', NULL, 1, NULL, NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `tipe_file` varchar(100) DEFAULT NULL,
  `ukuran_file` int(10) UNSIGNED DEFAULT NULL COMMENT 'dalam bytes',
  `kategori` enum('dokumen','foto','video','lainnya') DEFAULT 'dokumen',
  `referensi_tabel` varchar(50) DEFAULT NULL COMMENT 'nama tabel terkait',
  `referensi_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id record terkait',
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `nama_file`, `path_file`, `tipe_file`, `ukuran_file`, `kategori`, `referensi_tabel`, `referensi_id`, `uploaded_by`, `created_at`) VALUES
(1, 'surat_tugas_001.pdf', '/uploads/dokumen/surat_tugas_001.pdf', 'application/pdf', 245760, 'dokumen', 'penugasan', 1, 1, '2026-05-05 11:04:22'),
(2, 'foto_kegiatan_01.jpg', '/uploads/foto/foto_kegiatan_01.jpg', 'image/jpeg', 1048576, 'foto', 'sekolah_laporan', 1, 3, '2026-05-05 11:04:22'),
(3, 'foto_kegiatan_02.jpg', '/uploads/foto/foto_kegiatan_02.jpg', 'image/jpeg', 2097152, 'foto', 'sekolah_laporan', 1, 3, '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tipe` enum('info','warning','success','error') DEFAULT 'info',
  `judul` varchar(255) NOT NULL,
  `pesan` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `url_target` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `tipe`, `judul`, `pesan`, `is_read`, `url_target`, `created_at`) VALUES
(1, 2, 'success', 'Laporan Disetujui', 'Laporan SMA Negeri 1 Kota Baru telah disetujui', 0, '/laporan/1', '2026-05-05 11:04:22'),
(2, 3, 'info', 'Penugasan Baru', 'Anda mendapat penugasan monitoring workshop', 1, '/penugasan/1', '2026-05-05 11:04:22'),
(3, 1, 'warning', 'Deadline Laporan', '3 laporan belum disubmit, deadline besok', 0, '/laporan/pending', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE `paket` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_paket` varchar(200) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id`, `nama_paket`, `deskripsi`, `created_at`) VALUES
(1, 'Paket Workshop', 'Berisi modul dan ATK workshop', '2026-05-05 11:04:22'),
(2, 'Paket Bimtek', 'Berisi alat peraga dan modul', '2026-05-05 11:04:22'),
(3, 'Paket Sosialisasi', 'Berisi modul dan brosur', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `paket_detail`
--

CREATE TABLE `paket_detail` (
  `id` int(10) UNSIGNED NOT NULL,
  `paket_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_detail`
--

INSERT INTO `paket_detail` (`id`, `paket_id`, `item_id`, `jumlah`) VALUES
(1, 1, 1, 1),
(2, 1, 3, 1),
(3, 2, 1, 1),
(4, 2, 2, 1),
(5, 3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `paket_item`
--

CREATE TABLE `paket_item` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_item` varchar(200) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_item`
--

INSERT INTO `paket_item` (`id`, `nama_item`, `stok`, `satuan`) VALUES
(1, 'Modul Kurikulum Merdeka', 100, 'eksemplar'),
(2, 'Alat Peraga Teaching Factory', 20, 'unit'),
(3, 'Paket ATK', 50, 'paket');

-- --------------------------------------------------------

--
-- Table structure for table `paket_penugasan`
--

CREATE TABLE `paket_penugasan` (
  `id` int(10) UNSIGNED NOT NULL,
  `penugasan_id` int(10) UNSIGNED NOT NULL,
  `paket_id` int(10) UNSIGNED NOT NULL,
  `diterima_oleh` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('dikirim','diterima') DEFAULT 'dikirim',
  `tanggal_distribusi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_penugasan`
--

INSERT INTO `paket_penugasan` (`id`, `penugasan_id`, `paket_id`, `diterima_oleh`, `status`, `tanggal_distribusi`) VALUES
(1, 1, 1, 3, 'diterima', '2026-05-05 11:04:22'),
(2, 2, 2, 3, 'dikirim', '2026-05-05 11:04:22'),
(3, 3, 3, NULL, 'dikirim', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `grup` enum('umum','instansi','sistem') DEFAULT 'umum',
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `kunci`, `nilai`, `keterangan`, `grup`, `updated_by`, `updated_at`) VALUES
(1, 'nama_instansi', 'Dinas Pendidikan Kabupaten Makmur', 'Nama instansi resmi', 'instansi', NULL, '2026-05-05 11:04:22'),
(2, 'max_upload_size', '2097152', 'Ukuran maksimal upload file (bytes)', 'sistem', NULL, '2026-05-05 11:04:22'),
(3, 'notifikasi_email', 'notifikasi@dinas.go.id', 'Email pengirim notifikasi', 'sistem', NULL, '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `penugasan`
--

CREATE TABLE `penugasan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kegiatan_id` int(10) UNSIGNED DEFAULT NULL,
  `pengawas_id` int(10) UNSIGNED DEFAULT NULL,
  `nama_tugas` varchar(200) DEFAULT NULL,
  `tipe_penugasan` enum('personal','tim') DEFAULT NULL,
  `deskripsi_tugas` text DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `deadline_lapor` date DEFAULT NULL,
  `file_surat_tugas` varchar(255) DEFAULT NULL,
  `file_sppd` varchar(255) DEFAULT NULL,
  `status` enum('draft','berjalan','selesai') DEFAULT 'draft',
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penugasan`
--

INSERT INTO `penugasan` (`id`, `kegiatan_id`, `pengawas_id`, `nama_tugas`, `tipe_penugasan`, `deskripsi_tugas`, `tgl_mulai`, `tgl_selesai`, `deadline_lapor`, `file_surat_tugas`, `file_sppd`, `status`, `dibuat_oleh`, `dibuat_pada`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'Monitoring Workshop Kurikulum Merdeka', 'personal', 'Melakukan monitoring pelaksanaan workshop', '2026-06-10', '2026-06-12', '2026-06-19', NULL, NULL, 'selesai', 1, '2026-05-05 11:04:22', '2026-05-05 11:04:22', NULL),
(2, 2, 2, 'Bimtek Teaching Factory SMK', 'tim', 'Pelaksanaan bimbingan teknis teaching factory', '2026-07-15', '2026-07-16', '2026-07-23', NULL, NULL, 'berjalan', 1, '2026-05-05 11:04:22', '2026-05-05 11:04:22', NULL),
(3, 3, NULL, 'Sosialisasi Dapodik 2026', 'personal', 'Sosialisasi pengisian Dapodik terbaru', '2026-08-01', '2026-08-01', '2026-08-08', NULL, NULL, 'draft', 1, '2026-05-05 11:04:22', '2026-05-05 11:04:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_sekolah`
--

CREATE TABLE `penugasan_sekolah` (
  `id` int(10) UNSIGNED NOT NULL,
  `penugasan_id` int(10) UNSIGNED NOT NULL,
  `sekolah_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penugasan_sekolah`
--

INSERT INTO `penugasan_sekolah` (`id`, `penugasan_id`, `sekolah_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_user`
--

CREATE TABLE `penugasan_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `penugasan_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `role` enum('ketua','anggota') NOT NULL DEFAULT 'anggota'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penugasan_user`
--

INSERT INTO `penugasan_user` (`id`, `penugasan_id`, `user_id`, `role`) VALUES
(1, 1, 3, 'anggota'),
(2, 2, 3, 'ketua'),
(3, 2, 1, 'anggota');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `origin` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `event_type`, `details`, `ip_address`, `user_agent`, `origin`, `created_at`) VALUES
(1, 'FAILED_LOGIN', 'Percobaan login gagal username: test123', '10.0.0.1', 'Mozilla/5.0', NULL, '2026-05-05 11:04:22'),
(2, 'SQL_INJECTION', 'Percobaan SQL injection terdeteksi', '10.0.0.2', 'curl/7.0', 'http://evil.com', '2026-05-05 11:04:22'),
(3, 'RATE_LIMIT', 'Rate limit exceeded untuk API key a1b2c3d4', '192.168.1.100', 'App/1.0', 'https://app.dinas.go.id', '2026-05-05 11:04:22'),
(4, 'LOGIN_FAILED', 'User not found: admin', '180.241.54.178', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'https://humasapp.uinsyahada.ac.id', '2026-05-05 11:05:18'),
(5, 'LOGIN_FAILED', 'Failed attempt for user: admin_utama', '180.241.54.178', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'https://humasapp.uinsyahada.ac.id', '2026-05-05 11:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(10) UNSIGNED NOT NULL,
  `npsn` varchar(20) NOT NULL,
  `nama_sekolah` varchar(200) DEFAULT NULL,
  `jenjang` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `akreditasi` varchar(10) DEFAULT NULL,
  `jalan` text DEFAULT NULL,
  `desa_kelurahan` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kabupaten` varchar(100) DEFAULT NULL,
  `propinsi` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_kepsek` varchar(100) DEFAULT NULL,
  `kontak_kepsek` varchar(20) DEFAULT NULL,
  `nama_operator` varchar(100) DEFAULT NULL,
  `kontak_operator` varchar(20) DEFAULT NULL,
  `jlh_siswa_X` int(11) DEFAULT NULL,
  `jlh_siswa_XI` int(11) DEFAULT NULL,
  `jlh_siswa_XII` int(11) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status_data` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `update_data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `update_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id`, `npsn`, `nama_sekolah`, `jenjang`, `status`, `akreditasi`, `jalan`, `desa_kelurahan`, `kecamatan`, `kabupaten`, `propinsi`, `website`, `email`, `nama_kepsek`, `kontak_kepsek`, `nama_operator`, `kontak_operator`, `jlh_siswa_X`, `jlh_siswa_XI`, `jlh_siswa_XII`, `tahun_ajaran`, `latitude`, `longitude`, `status_data`, `update_data`, `update_by`) VALUES
(1, '20500001', 'SMA Negeri 1 Kota Baru', 'SMA', 'Negeri', 'A', 'Jl. Pendidikan No. 1', 'Kelurahan Maju', 'Kecamatan Jaya', 'Kabupaten Makmur', 'Jawa Timur', NULL, NULL, 'Drs. Supriono, M.Pd.', '081300000001', NULL, NULL, NULL, NULL, NULL, NULL, -7.250445, 112.768845, 'aktif', '2026-05-05 11:04:22', 1),
(2, '20500002', 'SMA Negeri 2 Kota Baru', 'SMA', 'Negeri', 'A', 'Jl. Merdeka No. 45', 'Kelurahan Sejahtera', 'Kecamatan Jaya', 'Kabupaten Makmur', 'Jawa Timur', NULL, NULL, 'Hj. Nurhayati, S.Pd., M.M.', '081300000002', NULL, NULL, NULL, NULL, NULL, NULL, -7.260445, 112.778845, 'aktif', '2026-05-05 11:04:22', 1),
(3, '30500001', 'SMK Negeri 1 Kota Baru', 'SMK', 'Negeri', 'B', 'Jl. Industri No. 10', 'Kelurahan Karya', 'Kecamatan Jaya', 'Kabupaten Makmur', 'Jawa Timur', NULL, NULL, 'Drs. Haryono, M.T.', '081300000003', NULL, NULL, NULL, NULL, NULL, NULL, -7.270445, 112.788845, 'aktif', '2026-05-05 11:04:22', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_laporan`
--

CREATE TABLE `sekolah_laporan` (
  `id` int(10) UNSIGNED NOT NULL,
  `sekolah_id` int(10) UNSIGNED DEFAULT NULL,
  `kegiatan_id` int(10) UNSIGNED DEFAULT NULL,
  `penugasan_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `pengawas_id` int(10) UNSIGNED DEFAULT NULL,
  `snapshot_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot data sekolah saat laporan dibuat' CHECK (json_valid(`snapshot_data`)),
  `foto_laporan_satu` varchar(255) DEFAULT NULL,
  `foto_laporan_dua` varchar(255) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status_pengajuan` enum('draft','diajukan','ditinjau','disetujui','ditolak') NOT NULL DEFAULT 'draft',
  `validasi_pengawas` enum('pending','setuju','tolak') NOT NULL DEFAULT 'pending',
  `catatan_user` text DEFAULT NULL,
  `catatan_pengawas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_laporan`
--

INSERT INTO `sekolah_laporan` (`id`, `sekolah_id`, `kegiatan_id`, `penugasan_id`, `user_id`, `pengawas_id`, `snapshot_data`, `foto_laporan_satu`, `foto_laporan_dua`, `latitude`, `longitude`, `status_pengajuan`, `validasi_pengawas`, `catatan_user`, `catatan_pengawas`, `created_at`, `submitted_at`, `reviewed_at`, `approved_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 3, 2, '{\"nama_sekolah\":\"SMA Negeri 1 Kota Baru\",\"akreditasi\":\"A\",\"kepsek\":\"Drs. Supriono, M.Pd.\"}', NULL, NULL, -7.250445, 112.768845, 'disetujui', 'setuju', 'Kegiatan berjalan lancar, 50 guru hadir', NULL, '2026-05-05 11:04:22', NULL, NULL, NULL, '2026-05-05 11:04:22', NULL),
(2, 2, 1, 1, 3, 2, '{\"nama_sekolah\":\"SMA Negeri 2 Kota Baru\",\"akreditasi\":\"A\",\"kepsek\":\"Hj. Nurhayati, S.Pd., M.M.\"}', NULL, NULL, -7.260445, 112.778845, 'ditinjau', 'pending', 'Sedang dalam review pengawas', NULL, '2026-05-05 11:04:22', NULL, NULL, NULL, '2026-05-05 11:04:22', NULL),
(3, 3, 2, 2, 3, 2, '{\"nama_sekolah\":\"SMK Negeri 1 Kota Baru\",\"akreditasi\":\"B\",\"kepsek\":\"Drs. Haryono, M.T.\"}', NULL, NULL, -7.270445, 112.788845, 'diajukan', 'pending', 'Membutuhkan peralatan teaching factory', NULL, '2026-05-05 11:04:22', NULL, NULL, NULL, '2026-05-05 11:04:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_pengajuan`
--

CREATE TABLE `sekolah_pengajuan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_pengajuan`
--

INSERT INTO `sekolah_pengajuan` (`id`, `user_id`, `status`, `keterangan`, `created_at`) VALUES
(1, 3, 'diterima', 'Pengajuan data sekolah baru', '2026-05-05 11:04:22'),
(2, 3, 'pending', 'Menunggu verifikasi', '2026-05-05 11:04:22'),
(3, 1, 'ditolak', 'Data tidak lengkap', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_survey` varchar(200) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey`
--

INSERT INTO `survey` (`id`, `nama_survey`, `deskripsi`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Survey Kepuasan Workshop', 'Survey tingkat kepuasan peserta workshop', 1, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(2, 'Survey Kesiapan Teaching Factory', 'Survey kesiapan SMK menerapkan teaching factory', 2, '2026-05-05 11:04:22', '2026-05-05 11:04:22'),
(3, 'Survey Pemahaman Dapodik', 'Survey pemahaman operator tentang Dapodik', 1, '2026-05-05 11:04:22', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `survey_jawaban`
--

CREATE TABLE `survey_jawaban` (
  `id` int(10) UNSIGNED NOT NULL,
  `laporan_id` int(10) UNSIGNED NOT NULL,
  `survey_id` int(10) UNSIGNED DEFAULT NULL,
  `pertanyaan_id` int(10) UNSIGNED NOT NULL,
  `jawaban_text` text DEFAULT NULL,
  `opsi_id` int(10) UNSIGNED DEFAULT NULL,
  `opsi_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_jawaban`
--

INSERT INTO `survey_jawaban` (`id`, `laporan_id`, `survey_id`, `pertanyaan_id`, `jawaban_text`, `opsi_id`, `opsi_value`, `created_at`) VALUES
(1, 1, 1, 1, NULL, NULL, '5', '2026-05-05 11:04:22'),
(2, 1, 1, 2, NULL, 1, 'Sangat Memadai', '2026-05-05 11:04:22'),
(3, 3, 2, 3, NULL, 2, 'Sebagian', '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `survey_laporan`
--

CREATE TABLE `survey_laporan` (
  `id` int(10) UNSIGNED NOT NULL,
  `penugasan_id` int(10) UNSIGNED NOT NULL,
  `sekolah_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_laporan`
--

INSERT INTO `survey_laporan` (`id`, `penugasan_id`, `sekolah_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, 3, '2026-05-05 11:04:22'),
(2, 1, 2, 3, '2026-05-05 11:04:22'),
(3, 2, 3, 3, '2026-05-05 11:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `survey_opsi`
--

CREATE TABLE `survey_opsi` (
  `id` int(10) UNSIGNED NOT NULL,
  `pertanyaan_id` int(10) UNSIGNED NOT NULL,
  `opsi` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_opsi`
--

INSERT INTO `survey_opsi` (`id`, `pertanyaan_id`, `opsi`, `sort_order`) VALUES
(1, 2, 'Sangat Memadai', 1),
(2, 2, 'Cukup Memadai', 2),
(3, 2, 'Kurang Memadai', 3),
(4, 3, 'Sudah Lengkap', 1),
(5, 3, 'Sebagian', 2),
(6, 3, 'Belum Ada', 3);

-- --------------------------------------------------------

--
-- Table structure for table `survey_pertanyaan`
--

CREATE TABLE `survey_pertanyaan` (
  `id` int(10) UNSIGNED NOT NULL,
  `survey_id` int(10) UNSIGNED NOT NULL,
  `pertanyaan` text DEFAULT NULL,
  `tipe_jawaban` enum('text','textarea','radio','dropdown','checkbox','multiselect','rating') DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_pertanyaan`
--

INSERT INTO `survey_pertanyaan` (`id`, `survey_id`, `pertanyaan`, `tipe_jawaban`, `sort_order`) VALUES
(1, 1, 'Seberapa puas Anda terhadap materi workshop?', 'rating', 1),
(2, 1, 'Apakah fasilitas memadai?', 'radio', 2),
(3, 2, 'Apakah sekolah memiliki peralatan teaching factory?', 'radio', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kontak_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','pengawas','pelaksana') NOT NULL,
  `status` enum('nonaktif','pending','aktif') NOT NULL DEFAULT 'pending',
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `alamat_lengkap`, `nip`, `jabatan`, `email`, `kontak_hp`, `role`, `status`, `foto_profil`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin_utama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ahmad Fauzi, M.Pd.', NULL, '198001012010011001', 'Kepala Dinas', 'ahmad.fauzi@dinas.go.id', '081234567890', 'admin', 'aktif', NULL, '2026-05-05 11:04:22', '2026-05-05 11:06:10', NULL),
(2, 'pengawas_budi', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Budi Santoso, S.Pd.', NULL, '198502152011011002', 'Pengawas SMA', 'budi.santoso@dinas.go.id', '081234567891', 'pengawas', 'aktif', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22', NULL),
(3, 'pelaksana_ani', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Ani Rahmawati, S.Kom.', NULL, '199003202012012003', 'Staf IT', 'ani.rahmawati@dinas.go.id', '081234567892', 'pelaksana', 'aktif', NULL, '2026-05-05 11:04:22', '2026-05-05 11:04:22', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_hash` (`api_key_hash`);

--
-- Indexes for table `api_key_domains`
--
ALTER TABLE `api_key_domains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_api_key` (`api_key_id`);

--
-- Indexes for table `api_resources`
--
ALTER TABLE `api_resources`
  ADD PRIMARY KEY (`table_name`);

--
-- Indexes for table `domain_whitelist`
--
ALTER TABLE `domain_whitelist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_domain` (`domain`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_domain_whitelist_added_by` (`added_by`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kegiatan_tor` (`tor_id`),
  ADD KEY `idx_kegiatan_proposal` (`proposal_id`),
  ADD KEY `idx_kegiatan_laporan` (`laporan_id`),
  ADD KEY `fk_kegiatan_created_by` (`created_by`),
  ADD KEY `fk_kegiatan_updated_by` (`updated_by`);

--
-- Indexes for table `kegiatan_jadwal`
--
ALTER TABLE `kegiatan_jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jadwal_kegiatan` (`kegiatan_id`);

--
-- Indexes for table `kegiatan_laporan`
--
ALTER TABLE `kegiatan_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laporan_created_by` (`created_by`),
  ADD KEY `fk_laporan_updated_by` (`updated_by`);
ALTER TABLE `kegiatan_laporan` ADD FULLTEXT KEY `ft_laporan` (`lpj_nama_kegiatan`,`lpj_tujuan`,`lpj_sasaran`);

--
-- Indexes for table `kegiatan_proposal`
--
ALTER TABLE `kegiatan_proposal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_proposal_created_by` (`created_by`),
  ADD KEY `fk_proposal_updated_by` (`updated_by`);
ALTER TABLE `kegiatan_proposal` ADD FULLTEXT KEY `ft_proposal` (`pro_nama_kegiatan`,`pro_tujuan`,`pro_sasaran`);

--
-- Indexes for table `kegiatan_rab_item`
--
ALTER TABLE `kegiatan_rab_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rab_kegiatan` (`kegiatan_id`);

--
-- Indexes for table `kegiatan_tor`
--
ALTER TABLE `kegiatan_tor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tor_created_by` (`created_by`),
  ADD KEY `fk_tor_updated_by` (`updated_by`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ref` (`referensi_tabel`,`referensi_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`),
  ADD KEY `idx_notif_read` (`user_id`,`is_read`),
  ADD KEY `idx_notif_created` (`created_at`);

--
-- Indexes for table `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_detail`
--
ALTER TABLE `paket_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pd_paket` (`paket_id`),
  ADD KEY `idx_pd_item` (`item_id`);

--
-- Indexes for table `paket_item`
--
ALTER TABLE `paket_item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nama_item` (`nama_item`);

--
-- Indexes for table `paket_penugasan`
--
ALTER TABLE `paket_penugasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pp_penugasan` (`penugasan_id`),
  ADD KEY `idx_pp_paket` (`paket_id`),
  ADD KEY `idx_diterima_oleh` (`diterima_oleh`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunci` (`kunci`),
  ADD KEY `fk_pengaturan_updated_by` (`updated_by`);

--
-- Indexes for table `penugasan`
--
ALTER TABLE `penugasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kegiatan_penugasan` (`kegiatan_id`),
  ADD KEY `idx_pengawas_penugasan` (`pengawas_id`),
  ADD KEY `idx_status_penugasan` (`status`),
  ADD KEY `idx_tanggal` (`tgl_mulai`,`tgl_selesai`),
  ADD KEY `idx_dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `penugasan_sekolah`
--
ALTER TABLE `penugasan_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ps_penugasan` (`penugasan_id`),
  ADD KEY `idx_ps_sekolah` (`sekolah_id`);

--
-- Indexes for table `penugasan_user`
--
ALTER TABLE `penugasan_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pu_penugasan` (`penugasan_id`),
  ADD KEY `idx_pu_user` (`user_id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`event_type`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_type_created` (`event_type`,`created_at`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_npsn` (`npsn`),
  ADD KEY `idx_wilayah` (`propinsi`,`kabupaten`),
  ADD KEY `idx_status` (`status_data`),
  ADD KEY `idx_status_wilayah` (`status_data`,`propinsi`,`kabupaten`),
  ADD KEY `idx_update_by` (`update_by`);

--
-- Indexes for table `sekolah_laporan`
--
ALTER TABLE `sekolah_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sh_sekolah` (`sekolah_id`),
  ADD KEY `idx_sh_kegiatan` (`kegiatan_id`),
  ADD KEY `idx_sh_penugasan` (`penugasan_id`),
  ADD KEY `idx_sh_user` (`user_id`),
  ADD KEY `idx_sh_pengawas` (`pengawas_id`),
  ADD KEY `idx_sh_status_pengajuan` (`status_pengajuan`),
  ADD KEY `idx_sh_keg_sek_status` (`kegiatan_id`,`sekolah_id`,`status_pengajuan`);

--
-- Indexes for table `sekolah_pengajuan`
--
ALTER TABLE `sekolah_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_spg_user` (`user_id`),
  ADD KEY `idx_spg_status` (`status`);

--
-- Indexes for table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_survey_created_by` (`created_by`);

--
-- Indexes for table `survey_jawaban`
--
ALTER TABLE `survey_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sj_laporan` (`laporan_id`),
  ADD KEY `idx_sj_pertanyaan` (`pertanyaan_id`),
  ADD KEY `idx_sj_opsi` (`opsi_id`),
  ADD KEY `idx_survey` (`survey_id`),
  ADD KEY `idx_lap_pert` (`laporan_id`,`pertanyaan_id`);

--
-- Indexes for table `survey_laporan`
--
ALTER TABLE `survey_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sl_penugasan` (`penugasan_id`),
  ADD KEY `idx_sl_sekolah` (`sekolah_id`),
  ADD KEY `idx_sl_user` (`user_id`);

--
-- Indexes for table `survey_opsi`
--
ALTER TABLE `survey_opsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_so_pertanyaan` (`pertanyaan_id`);

--
-- Indexes for table `survey_pertanyaan`
--
ALTER TABLE `survey_pertanyaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sp_survey` (`survey_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_username` (`username`),
  ADD UNIQUE KEY `uniq_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `api_key_domains`
--
ALTER TABLE `api_key_domains`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `domain_whitelist`
--
ALTER TABLE `domain_whitelist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kegiatan_jadwal`
--
ALTER TABLE `kegiatan_jadwal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kegiatan_laporan`
--
ALTER TABLE `kegiatan_laporan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kegiatan_proposal`
--
ALTER TABLE `kegiatan_proposal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kegiatan_rab_item`
--
ALTER TABLE `kegiatan_rab_item`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kegiatan_tor`
--
ALTER TABLE `kegiatan_tor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paket`
--
ALTER TABLE `paket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paket_detail`
--
ALTER TABLE `paket_detail`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paket_item`
--
ALTER TABLE `paket_item`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paket_penugasan`
--
ALTER TABLE `paket_penugasan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penugasan`
--
ALTER TABLE `penugasan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penugasan_sekolah`
--
ALTER TABLE `penugasan_sekolah`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penugasan_user`
--
ALTER TABLE `penugasan_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sekolah_laporan`
--
ALTER TABLE `sekolah_laporan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sekolah_pengajuan`
--
ALTER TABLE `sekolah_pengajuan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey`
--
ALTER TABLE `survey`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_jawaban`
--
ALTER TABLE `survey_jawaban`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_laporan`
--
ALTER TABLE `survey_laporan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_opsi`
--
ALTER TABLE `survey_opsi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `survey_pertanyaan`
--
ALTER TABLE `survey_pertanyaan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD CONSTRAINT `api_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `api_key_domains`
--
ALTER TABLE `api_key_domains`
  ADD CONSTRAINT `api_key_domains_ibfk_1` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `domain_whitelist`
--
ALTER TABLE `domain_whitelist`
  ADD CONSTRAINT `fk_domain_whitelist_added_by` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `fk_kegiatan_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kegiatan_laporan` FOREIGN KEY (`laporan_id`) REFERENCES `kegiatan_laporan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `kegiatan_proposal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan_tor` FOREIGN KEY (`tor_id`) REFERENCES `kegiatan_tor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kegiatan_jadwal`
--
ALTER TABLE `kegiatan_jadwal`
  ADD CONSTRAINT `fk_jadwal_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kegiatan_laporan`
--
ALTER TABLE `kegiatan_laporan`
  ADD CONSTRAINT `fk_laporan_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_laporan_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kegiatan_proposal`
--
ALTER TABLE `kegiatan_proposal`
  ADD CONSTRAINT `fk_proposal_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_proposal_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kegiatan_rab_item`
--
ALTER TABLE `kegiatan_rab_item`
  ADD CONSTRAINT `fk_rab_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kegiatan_tor`
--
ALTER TABLE `kegiatan_tor`
  ADD CONSTRAINT `fk_tor_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tor_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paket_detail`
--
ALTER TABLE `paket_detail`
  ADD CONSTRAINT `fk_pd_item` FOREIGN KEY (`item_id`) REFERENCES `paket_item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pd_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paket_penugasan`
--
ALTER TABLE `paket_penugasan`
  ADD CONSTRAINT `fk_pp_diterima_oleh` FOREIGN KEY (`diterima_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pp_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_penugasan` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD CONSTRAINT `fk_pengaturan_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penugasan`
--
ALTER TABLE `penugasan`
  ADD CONSTRAINT `fk_penugasan_dibuat_oleh` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_penugasan_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penugasan_pengawas` FOREIGN KEY (`pengawas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penugasan_sekolah`
--
ALTER TABLE `penugasan_sekolah`
  ADD CONSTRAINT `fk_ps_penugasan` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penugasan_user`
--
ALTER TABLE `penugasan_user`
  ADD CONSTRAINT `fk_pu_penugasan` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD CONSTRAINT `fk_sekolah_update_by` FOREIGN KEY (`update_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah_laporan`
--
ALTER TABLE `sekolah_laporan`
  ADD CONSTRAINT `fk_sh_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sh_pengawas` FOREIGN KEY (`pengawas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sh_penugasan` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sh_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah_pengajuan`
--
ALTER TABLE `sekolah_pengajuan`
  ADD CONSTRAINT `fk_spg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `survey`
--
ALTER TABLE `survey`
  ADD CONSTRAINT `fk_survey_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `survey_jawaban`
--
ALTER TABLE `survey_jawaban`
  ADD CONSTRAINT `fk_sj_laporan` FOREIGN KEY (`laporan_id`) REFERENCES `survey_laporan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sj_opsi` FOREIGN KEY (`opsi_id`) REFERENCES `survey_opsi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sj_pertanyaan` FOREIGN KEY (`pertanyaan_id`) REFERENCES `survey_pertanyaan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sj_survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_laporan`
--
ALTER TABLE `survey_laporan`
  ADD CONSTRAINT `fk_sl_penugasan` FOREIGN KEY (`penugasan_id`) REFERENCES `penugasan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sl_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `survey_opsi`
--
ALTER TABLE `survey_opsi`
  ADD CONSTRAINT `fk_so_pertanyaan` FOREIGN KEY (`pertanyaan_id`) REFERENCES `survey_pertanyaan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_pertanyaan`
--
ALTER TABLE `survey_pertanyaan`
  ADD CONSTRAINT `fk_sp_survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
