-- SIPER v2.0 - Database Schema
-- Sistem Perencanaan Keuangan (Financial Planning System)

CREATE DATABASE IF NOT EXISTS perencanaan_keuangan;
USE perencanaan_keuangan;

-- Set timezone
SET time_zone = '+07:00';

-- Users table
CREATE TABLE IF NOT EXISTS pengguna (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    foto_profil VARCHAR(255) DEFAULT 'default.png',
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Expense accounts table
CREATE TABLE IF NOT EXISTS akun_belanja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_akun VARCHAR(20) UNIQUE NOT NULL,
    nama_akun VARCHAR(200) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Budget types table
CREATE TABLE IF NOT EXISTS jenis_pagu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_jenis VARCHAR(100) UNIQUE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Execution units table
CREATE TABLE IF NOT EXISTS unit_pelaksana (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_unit VARCHAR(100) UNIQUE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Main budget allocations table
CREATE TABLE IF NOT EXISTS pagu_anggaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul_kegiatan VARCHAR(255) NOT NULL,
    nama_pagu VARCHAR(200) NOT NULL,
    jenis_id INT NOT NULL,
    unit_id INT NOT NULL,
    nominal_pagu DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (jenis_id) REFERENCES jenis_pagu(id) ON DELETE RESTRICT,
    FOREIGN KEY (unit_id) REFERENCES unit_pelaksana(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES pengguna(id) ON DELETE RESTRICT
);

-- Programs within budgets table
CREATE TABLE IF NOT EXISTS program_pagu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagu_id INT NOT NULL,
    nama_program VARCHAR(255) NOT NULL,
    total_program DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pagu_id) REFERENCES pagu_anggaran(id) ON DELETE CASCADE
);

-- Budget details/items table
CREATE TABLE IF NOT EXISTS uraian_anggaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT NOT NULL,
    akun_id INT NOT NULL,
    uraian_kegiatan TEXT NOT NULL,
    volume DECIMAL(10,2) NOT NULL,
    satuan VARCHAR(50) NOT NULL,
    harga_satuan DECIMAL(12,2) NOT NULL,
    jumlah DECIMAL(15,2) GENERATED ALWAYS AS (volume * harga_satuan) STORED,
    nilai_blokir DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES program_pagu(id) ON DELETE CASCADE,
    FOREIGN KEY (akun_id) REFERENCES akun_belanja(id) ON DELETE RESTRICT
);

-- Insert default admin user
INSERT INTO pengguna (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Insert sample data for testing
INSERT INTO akun_belanja (kode_akun, nama_akun, keterangan) VALUES
('511111', 'Belanja Pegawai', 'Belanja untuk gaji dan tunjangan pegawai'),
('511211', 'Belanja Barang', 'Belanja untuk kebutuhan barang habis pakai'),
('521111', 'Belanja Modal', 'Belanja untuk aset tetap')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO jenis_pagu (nama_jenis, keterangan) VALUES
('APBD', 'Anggaran Pendapatan dan Belanja Daerah'),
('APBN', 'Anggaran Pendapatan dan Belanja Negara'),
('Dana Desa', 'Dana untuk pembangunan desa')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO unit_pelaksana (nama_unit, keterangan) VALUES
('Dinas Pendidikan', 'Unit pelaksana bidang pendidikan'),
('Dinas Kesehatan', 'Unit pelaksana bidang kesehatan'),
('Dinas Pekerjaan Umum', 'Unit pelaksana bidang infrastruktur')
ON DUPLICATE KEY UPDATE id=id;

-- Create indexes for better performance
CREATE INDEX idx_pagu_created_by ON pagu_anggaran(created_by);
CREATE INDEX idx_program_pagu_id ON program_pagu(pagu_id);
CREATE INDEX idx_uraian_program_id ON uraian_anggaran(program_id);
CREATE INDEX idx_uraian_akun_id ON uraian_anggaran(akun_id);