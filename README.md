# Angplov Undangan Digital

Proyek ini adalah platform berbasis WordPress untuk membuat undangan pernikahan digital, yang telah dikemas menggunakan Docker untuk kemudahan deployment dan pengembangan.

## Fitur

- **Lingkungan Docker**: Menggunakan Docker Container yang terisolasi dan konsisten.
- **Ringan**: Menggunakan konfigurasi yang dioptimalkan.
- **Auto-Config**: Database dan koneksi dikonfigurasi secara otomatis melalui environment variables.

- **Database Siap Pakai**: Database (`init.sql`) akan di-restore secara otomatis ke versi terakhir (termasuk instalasi plugin, tema, dan preferensi WordPress).
- **Binding Lokal**: Seluruh *source code* (plugin, tema, konfigurasi) berada di map saat ini dan otomatis terbaca oleh kontainer.

## Persyaratan Sistem

- Docker
- Docker Compose

## Cara Menjalankan

1. **Jalankan Container**:
   Buka terminal di direktori proyek dan jalankan perintah berikut:
   ```bash
   docker compose up -d
   ```
   > **Catatan:** Pada saat pertama kali dijalankan, proses ini akan memakan waktu sejenak karena image database MariaDB akan melakukan impor otomatis dari file `init.sql`.

2. **Akses Website**:
   Setelah kontainer menyala, website sudah dalam keadaan **lengkap terinstal** dan tidak akan meminta *setup wizard* lagi. Website dapat langsung diakses melalui browser di alamat:
   - URL: [http://localhost:8084](http://localhost:8084)
   - URL Admin: [http://localhost:8084/wp-admin/](http://localhost:8084/wp-admin/)
     - Username: `admin` atau sesuai user yang telah Anda buat sebelumnya.
     - Password: `admin`

## Konfigurasi Teknis

- **Port Aplikasi**: `8084`
- **Database**: MariaDB 10
- **Web Server**: Apache (via WordPress image)
- **PHP Version**: 8.x (sesuai image WordPress terbaru)

### Kredensial Database Docker
Database dirancang untuk terkonfigurasi secara otomatis dari file `init.sql`.
- **Host**: `db`
- **Database**: `wordpress`
- **User**: `wordpress`
- **Password**: `wordpress`

## Troubleshooting

- Jika terjadi *error koneksi database*, tunggu beberapa detik karena MariaDB masih mengimpor file `init.sql`. Anda dapat memantau prosesnya dengan perintah:
  ```bash
  docker compose logs -f db
  ```
- Jika ada masalah *permission denied* saat mengunduh/mengupload media atau plugin dari wp-admin, kontainer sudah dikonfigurasikan dengan `user: "33:33"` untuk menyamai www-data. Jika masih gagal, ubah *ownership* file lokal dengan perintah `sudo chown -R 33:33 wp-content/`.

## Screenshots

![Screenshot 1](./Screenshot%20(34).png)
![Screenshot 2](./Screenshot%20(35).png)
![Screenshot 3](./Screenshot%20(36).png)
![Screenshot 4](./Screenshot%20(37).png)
