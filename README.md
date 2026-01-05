# Angplov Undangan Digital

Proyek ini adalah platform berbasis WordPress untuk membuat undangan pernikahan digital, yang telah dikemas menggunakan Docker untuk kemudahan deployment dan pengembangan.

## Fitur

- **Lingkungan Docker**: Menggunakan Docker Container yang terisolasi dan konsisten.
- **Ringan**: Menggunakan konfigurasi yang dioptimalkan.
- **Auto-Config**: Database dan koneksi dikonfigurasi secara otomatis melalui environment variables.

## Persyaratan Sistem

- Docker
- Docker Compose

## Cara Menjalankan

1. **Jalankan Container**:
   Buka terminal di direktori proyek dan jalankan perintah berikut:
   ```bash
   docker compose up -d
   ```

2. **Akses Website**:
   Website dapat diakses melalui browser di alamat:
   - URL Lokal: [http://localhost:8084](http://localhost:8084)
   - Domain Konfigurasi: `http://undangan.widifirmaan.web.id`

   *Catatan: Untuk mengakses menggunakan domain konfigurasi di komputer lokal, tambahkan entry berikut ke file `/etc/hosts` (Linux/Mac) atau `C:\Windows\System32\drivers\etc\hosts` (Windows):*
   ```
   127.0.0.1 undangan.widifirmaan.web.id
   ```

## Konfigurasi Teknis

- **Port Aplikasi**: `8084`
- **Database**: MariaDB 10
- **Web Server**: Apache (via WordPress image)
- **PHP Version**: 8.x (sesuai image WordPress terbaru)

### Kredensial Database
Database dibuat secara otomatis saat pertama kali dijalankan.
- **Host**: `db`
- **Database**: `wordpress`
- **User**: `wordpress`
- **Password**: `wordpress`

## Catatan Penting

- Jika Anda menjalankan proyek ini untuk pertama kalinya dan tidak ada database backup yang ditemukan, WordPress akan mengarahkan Anda ke halaman instalasi baru.
- Konfigurasi domain telah diatur di `wp-config.php`.

## Troubleshooting

Jika terjadi error koneksi database, tunggu beberapa saat karena MariaDB mungkin sedang dalam proses inisialisasi awal. Anda dapat merestart container wordpress dengan:
```bash
docker compose restart wordpress
```
