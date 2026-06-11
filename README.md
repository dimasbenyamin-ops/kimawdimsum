# Kumaw Dimsum

Aplikasi Point of Sales (POS) dan sistem pemesanan makanan berbasis web untuk Kumaw Dimsum. Aplikasi ini dibangun menggunakan Laravel dan memungkinkan pelanggan untuk memesan langsung melalui perangkat mereka (Guest Checkout), serta menyediakan dashboard khusus untuk Kasir dan Admin dalam mengelola pesanan, menu, dan laporan penjualan.

## Fitur Utama

### 🛒 Pelanggan (Customer / Public)
- **Katalog Menu:** Melihat daftar menu makanan yang tersedia.
- **Keranjang Belanja:** Menambahkan pesanan ke keranjang (berbasis session, tanpa perlu membuat akun/login).
- **Guest Checkout:** Melakukan proses checkout pesanan secara langsung.
- **Integrasi Pembayaran:** Pembayaran online yang terintegrasi dengan **Midtrans** (menggunakan Snap Token & Webhook Notification).

### 🖥️ Kasir (Cashier)
- **Dashboard Kasir:** Memantau pesanan masuk dari pelanggan secara real-time.
- **Manajemen Status Pesanan:** Memperbarui status pesanan pelanggan (seperti diproses, selesai, dll).
- **Point of Sales (POS):** Membuat pesanan langsung di meja kasir untuk pelanggan yang datang ke tempat (Dine-in / Takeaway).

### ⚙️ Admin
- **Dashboard Admin:** Ringkasan statistik dan aktivitas sistem.
- **Manajemen Menu:** Menambah, mengubah, dan menghapus daftar menu/makanan.
- **Laporan Pendapatan:** Melihat statistik dan laporan pendapatan penjualan.
- **Pengaturan QRIS:** Mengatur kode QRIS toko untuk pembayaran manual jika diperlukan.
- **Manajemen Pengguna (User Management):** Mengelola akun staff admin dan kasir.
- **Manajemen Role & Akses:** Mengelola peran (Role) dan hak akses (Permission) menu dinamis untuk setiap pengguna.

---

## Cara Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek ini secara lokal:

1. **Clone Repository**
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   cd KumawDimsum
   ```

2. **Install Dependensi Composer**
   Pastikan Anda sudah menginstal PHP dan Composer.
   ```bash
   composer install
   ```

3. **Install Dependensi Node.js (Aset Frontend)**
   Pastikan Anda sudah menginstal Node.js dan NPM.
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan konfigurasi database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_database_anda
   DB_PASSWORD=password_database_anda
   ```
   *(Catatan: Jangan lupa tambahkan juga kredensial API Keys Midtrans Anda di file `.env` agar fitur pembayaran dapat berfungsi)*

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database dan Seeding**
   Jalankan perintah ini untuk membuat tabel di database beserta data dummy atau data awal (seperti akun default Admin dan Kasir):
   ```bash
   php artisan migrate --seed
   ```

7. **Storage Link (Opsional jika ada fitur upload gambar)**
   ```bash
   php artisan storage:link
   ```

8. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses melalui browser pada alamat: `http://localhost:8000`
