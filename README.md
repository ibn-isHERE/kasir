# SISTEM KASIR - NATIVE PHP & MySQL

Sistem Point of Sale (POS) modern dengan fitur lengkap untuk mengelola transaksi penjualan, inventaris, dan laporan.

## 🚀 FITUR UTAMA

### Fitur Admin
1. **Dashboard Admin**
   - Statistik bisnis real-time
   - Monitor total produk, penjualan harian, pelanggan, dan petugas
   - Alert otomatis untuk stok rendah
   - Update stok cepat langsung dari dashboard
   - Riwayat transaksi terbaru

2. **Riwayat Transaksi**
   - Filter berdasarkan rentang tanggal
   - Detail lengkap setiap transaksi
   - Fitur hapus transaksi (dengan pengembalian stok)
   - Cetak nota per transaksi

3. **Data Produk**
   - CRUD produk (Create, Read, Update, Delete)
   - Monitoring stok real-time
   - Kalkulasi total nilai aset otomatis
   - Status stok (Aman/Rendah/Habis)

4. **Registrasi User**
   - Manajemen akun admin dan petugas
   - Pengaturan role (Admin/Petugas)
   - Keamanan password dengan MD5 hash

5. **Laporan**
   - Rekapitulasi penjualan per periode
   - Statistik performa per kasir
   - Produk terlaris
   - Cetak laporan lengkap

### Fitur Petugas
1. **Dashboard Petugas**
   - Greeting personal
   - Statistik transaksi harian
   - Total penjualan personal
   - Log transaksi terakhir

2. **Point of Sale (POS)**
   - Katalog produk visual dengan stok real-time
   - Sistem keranjang belanja interaktif
   - Input identitas pelanggan
   - Kalkulator pembayaran otomatis
   - Hitung kembalian secara real-time
   - Konfirmasi dan cetak nota otomatis

3. **Cek Stok Barang**
   - Monitoring ketersediaan produk
   - Status stok (Tersedia/Rendah/Habis)
   - Read-only untuk keamanan data

4. **Laporan Petugas**
   - Rekap transaksi personal
   - Filter berdasarkan periode
   - Total omset dan jumlah transaksi
   - Cetak laporan untuk serah terima shift

## 💻 TEKNOLOGI

- **Backend**: PHP Native (Tanpa Framework)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **Design**: Modern gradient UI dengan responsive layout

## 📋 INSTALASI

### 1. Persiapan
- XAMPP / WAMP / LAMP (PHP 7.4+ dan MySQL)
- Browser modern (Chrome, Firefox, Edge)

### 2. Instalasi Database
1. Buka phpMyAdmin
2. Import file `database.sql`
3. Database `dbkasir_pelanggan` akan terbuat otomatis

### 3. Konfigurasi
Edit `config.php` jika perlu:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dbkasir_pelanggan');
```

### 4. Akses Aplikasi
- URL: `http://localhost/sistem-kasir/`
- Login Admin: `admin` / `admin123`

## 📁 STRUKTUR FILE

```
sistem-kasir/
│
├── config.php              # Koneksi database
├── auth.php                # Autentikasi & session
├── index.php               # Halaman login
├── dashboard.php           # Dashboard (Admin & Petugas)
├── header.php              # Template header
├── footer.php              # Template footer
│
├── produk.php              # Manajemen produk (Admin)
├── user.php                # Manajemen user (Admin)
├── penjualan.php           # Riwayat transaksi (Admin)
├── laporan.php             # Laporan lengkap (Admin)
│
├── pos.php                 # Point of Sale (Petugas)
├── stok.php                # Cek stok (Petugas)
├── laporan_petugas.php     # Laporan petugas
│
├── detail_transaksi.php    # Detail transaksi
├── cetak_nota.php          # Cetak nota
├── cetak_laporan.php       # Cetak laporan admin
├── cetak_laporan_petugas.php # Cetak laporan petugas
├── logout.php              # Logout
│
├── database.sql            # SQL Database
└── README.md               # Dokumentasi
```

## 🔐 KEAMANAN

- Password hash dengan MD5
- Session management
- SQL Injection protection dengan escapeString
- Role-based access control (Admin/Petugas)
- Auto-logout pada session expired

## 🎨 DESAIN

- Modern gradient design
- Responsive layout untuk desktop dan mobile
- Clean and professional UI/UX
- Smooth animations dan transitions
- Color-coded alerts dan badges
- Print-friendly layouts untuk nota dan laporan

## 📱 RESPONSIVENESS

- Desktop: Full layout dengan sidebar
- Tablet: Adaptive grid
- Mobile: Collapsed sidebar, optimized layout

## 🔧 CUSTOMIZATION

### Mengubah Tema Warna
Edit variabel CSS di `header.php`:
```css
:root {
    --primary: #2d3436;
    --secondary: #00b894;
    --accent: #fdcb6e;
    --danger: #ff6b6b;
    /* dll */
}
```

### Menambah Produk Default
Edit file `database.sql` bagian INSERT produk.

### Stok Rendah Threshold
Default: 20 unit. Ubah di query:
```sql
WHERE Stok < 20  /* ubah angka sesuai kebutuhan */
```

## 🐛 TROUBLESHOOTING

### Error Koneksi Database
- Pastikan MySQL aktif
- Cek kredensial di `config.php`
- Pastikan database sudah di-import

### Session Error
- Cek `session_start()` di `auth.php`
- Clear browser cache dan cookies

### Permission Error
- Pastikan folder writable untuk web server
- Set permission 755 untuk folder

## 📞 SUPPORT

Untuk bantuan atau pertanyaan:
- Email: support@kasirpro.com
- Website: www.kasirpro.com

## 📄 LICENSE

© 2025 Kasir Pro - All Rights Reserved

---

**Sistem Kasir Native PHP - No Framework, Pure Power!**
