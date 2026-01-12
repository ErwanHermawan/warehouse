# Aplikasi Gudang (CI3 + HMVC) — Inbound & Outbound dengan QR code

Ringkasan:
- Framework: CodeIgniter 3
- Modular HMVC: gunakan wiredesignz (codeigniter-modular-extensions-hmvc)
- Fitur: Manajemen item, inbound (terima barang), outbound (keluar barang), generate QR untuk item, scan di browser untuk proses transaksi.
- QR generate: Google Chart API (mudah). Alternatif offline: phpqrcode.
- QR scan: html5-qrcode (browser, camera).

Persiapan:
1. Letakkan CodeIgniter 3 (system/) dan isi folder project dengan struktur HMVC.
2. Install HMVC (wiredesignz):
   - https://github.com/wiredesignz/codeigniter-modular-extensions-hmvc
   - Ikuti README: copy MX_Controller, MX_Loader, MX_Router ke application/core atau third_party sesuai instruksi.
3. Salin folder `application/` (berisi config/core) dan folder `modules/warehouse` dari project ini.
4. Sesuaikan `application/config/config.php` -> `$config['base_url']`
5. Konfigurasi database: `application/config/database.php`
6. Import database schema: lihat file `schema.sql` di bawah.
7. Akses aplikasi: http://localhost/your-project/

Catatan:
- QR generator menggunakan Google Chart API: tidak perlu library tambahan. Untuk produksi, pertimbangkan membuat QR di server menggunakan phpqrcode atau endroid/qr-code.
- Untuk scanning di browser, html5-qrcode butuh HTTPS pada beberapa browser untuk akses kamera. Untuk development, bisa pakai laptop dengan camera atau upload gambar QR (plugin html5-qrcode mendukung file input).
- Pastikan HMVC extension berfungsi; jika belum, wrapper MY_Controller/MY_Loader sudah disiapkan untuk fallback.
