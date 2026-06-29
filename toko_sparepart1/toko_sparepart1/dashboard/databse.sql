CREATE DATABASE toko_sparepart1;
USE toko_sparepart1;

-- ==========================
-- USERS
-- ==========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- KATEGORI
-- ==========================
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- ==========================
-- SUPPLIER
-- ==========================
CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100)
);

-- ==========================
-- PRODUK
-- ==========================
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(30) UNIQUE,
    nama_produk VARCHAR(150),
    kategori_id INT,
    supplier_id INT,
    merk VARCHAR(100),
    satuan VARCHAR(30),
    harga_beli DECIMAL(12,2),
    harga_jual DECIMAL(12,2),
    stok INT DEFAULT 0,
    stok_minimum INT DEFAULT 5,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(kategori_id) REFERENCES kategori(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

    FOREIGN KEY(supplier_id) REFERENCES supplier(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

-- ==========================
-- PELANGGAN
-- ==========================
CREATE TABLE pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    telepon VARCHAR(20),
    alamat TEXT
);

-- ==========================
-- PEMBELIAN
-- ==========================
CREATE TABLE pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_faktur VARCHAR(50),
    supplier_id INT,
    user_id INT,
    tanggal DATE,
    total DECIMAL(12,2),

    FOREIGN KEY(supplier_id) REFERENCES supplier(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

-- ==========================
-- DETAIL PEMBELIAN
-- ==========================
CREATE TABLE detail_pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembelian_id INT,
    produk_id INT,
    jumlah INT,
    harga DECIMAL(12,2),
    subtotal DECIMAL(12,2),

    FOREIGN KEY(pembelian_id) REFERENCES pembelian(id)
    ON DELETE CASCADE,

    FOREIGN KEY(produk_id) REFERENCES produk(id)
);

-- ==========================
-- PENJUALAN
-- ==========================
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_nota VARCHAR(50),
    pelanggan_id INT,
    user_id INT,
    tanggal DATE,
    total DECIMAL(12,2),
    bayar DECIMAL(12,2),
    kembalian DECIMAL(12,2),

    FOREIGN KEY(pelanggan_id) REFERENCES pelanggan(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

-- ==========================
-- DETAIL PENJUALAN
-- ==========================
CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT,
    produk_id INT,
    jumlah INT,
    harga DECIMAL(12,2),
    subtotal DECIMAL(12,2),

    FOREIGN KEY(penjualan_id) REFERENCES penjualan(id)
    ON DELETE CASCADE,

    FOREIGN KEY(produk_id) REFERENCES produk(id)
);

-- ==========================
-- STOK MASUK
-- ==========================
CREATE TABLE stok_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT,
    jumlah INT,
    tanggal DATE,
    keterangan VARCHAR(255),

    FOREIGN KEY(produk_id) REFERENCES produk(id)
);

-- ==========================
-- STOK KELUAR
-- ==========================
CREATE TABLE stok_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT,
    jumlah INT,
    tanggal DATE,
    keterangan VARCHAR(255),

    FOREIGN KEY(produk_id) REFERENCES produk(id)
);

-- ==========================
-- PENGATURAN TOKO
-- ==========================
CREATE TABLE pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_toko VARCHAR(100),
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    logo VARCHAR(255)
);