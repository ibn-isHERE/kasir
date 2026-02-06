-- Database Sistem Kasir
CREATE DATABASE IF NOT EXISTS dbkasir_pelanggan;
USE dbkasir_pelanggan;

-- Tabel User
CREATE TABLE IF NOT EXISTS user (
    UserID INT(11) PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('admin', 'petugas') NOT NULL DEFAULT 'petugas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
    PelangganID INT(11) PRIMARY KEY AUTO_INCREMENT,
    NamaPelanggan VARCHAR(255) NOT NULL,
    Alamat TEXT,
    NomorTelepon VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Produk
CREATE TABLE IF NOT EXISTS produk (
    ProdukID INT(11) PRIMARY KEY AUTO_INCREMENT,
    NamaProduk VARCHAR(255) NOT NULL,
    Harga DECIMAL(10,2) NOT NULL,
    Stok INT(11) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Penjualan
CREATE TABLE IF NOT EXISTS penjualan (
    PenjualanID INT(11) PRIMARY KEY AUTO_INCREMENT,
    TanggalPenjualan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    TotalHarga DECIMAL(10,2) NOT NULL,
    PelangganID INT(11) NOT NULL,
    UserID INT(11) NOT NULL,
    FOREIGN KEY (PelangganID) REFERENCES pelanggan(PelangganID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES user(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Detail Penjualan
CREATE TABLE IF NOT EXISTS detailpenjualan (
    DetailID INT(11) PRIMARY KEY AUTO_INCREMENT,
    PenjualanID INT(11) NOT NULL,
    ProdukID INT(11) NOT NULL,
    JumlahProduk INT(11) NOT NULL,
    Subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (PenjualanID) REFERENCES penjualan(PenjualanID) ON DELETE CASCADE,
    FOREIGN KEY (ProdukID) REFERENCES produk(ProdukID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data default admin
INSERT INTO user (Username, Password, Role) VALUES 
('admin', MD5('admin123'), 'admin');

-- Insert data sample produk
INSERT INTO produk (NamaProduk, Harga, Stok) VALUES 
('Indomie Goreng', 3500.00, 100),
('Aqua 600ml', 4000.00, 150),
('Teh Botol Sosro', 5000.00, 80),
('Mie Sedaap', 3000.00, 120),
('Coca Cola 330ml', 6000.00, 90);

-- Insert data sample pelanggan
INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES 
('Umum', '-', '-');
