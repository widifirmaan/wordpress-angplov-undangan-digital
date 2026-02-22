# 💌 Angplov Undangan Digital - Modern Wedding Invitation Platform

**Angplov** is a professional, WordPress-based digital invitation platform designed to simplify the creation and management of wedding invitations. Fully containerized with **Docker**, it offers a seamless deployment experience with pre-configured themes, plugins, and a ready-to-use database.

![Status](https://img.shields.io/badge/Status-Stable-success?style=for-the-badge)
![WordPress](https://img.shields.io/badge/WordPress-6.x-blue?style=for-the-badge&logo=wordpress)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue?style=for-the-badge&logo=docker)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-10-003545?style=for-the-badge&logo=mariadb&logoColor=white)

---

## 📸 Application Showcase

Explore the features of **Angplov** through our gallery.

| | |
|:---:|:---:|
| ![Screenshot 1](./Screenshot%20(34).png)<br>**Landing Page** | ![Screenshot 2](./Screenshot%20(35).png)<br>**Invitation View** |
| ![Screenshot 3](./Screenshot%20(36).png)<br>**Admin Dashboard** | ![Screenshot 4](./Screenshot%20(37).png)<br>**Plugin Management** |

---

## 🚀 Features Overview

2. **Akses Website**:
   Setelah kontainer menyala, website sudah dalam keadaan **lengkap terinstal** dan tidak akan meminta *setup wizard* lagi. Website dapat langsung diakses melalui browser di alamat:
   - URL: [http://localhost:8084](http://localhost:8084)
   - URL Admin: [http://localhost:8084/wp-admin/](http://localhost:8084/wp-admin/)
     - Username: `admin` atau sesuai user yang telah Anda buat sebelumnya.
     - Password: `admin`

### ⚡ Optimized Performance
*   **Lightweight**: Minimal configurations for fast response times and efficient resource usage.
*   **Production Ready**: Pre-configured with `.htaccess` rules and WordPress best practices.

### 🔄 Auto-Config & Sync
*   **Environment Driven**: Database and site configurations are handled via Docker environment variables.
*   **Volume Persistence**: Sync your `wp-content` directly from the host machine for seamless theme and plugin development.

---

## 🛠 Tech Stack

### Infrastructure
*   **Containerization**: Docker & Docker Compose
*   **Web Server**: Apache (WordPress Official Image)
*   **Database**: MariaDB 10

### Software
*   **Core**: WordPress 6.x
*   **Language**: PHP 8.x
*   **Tools**: WP-CLI, Git

---

## 📂 Project Structure

```bash
/
├── wp-content/             # Themes, Plugins, and Uploads (Synced to Host)
├── init.sql                # Database initialization script
├── docker-compose.yml      # Docker service definitions
├── wp-config.php           # Main WordPress configuration
├── .htaccess               # Web server configuration
└── README.md               # Project documentation
```

---

## 📦 Getting Started

### Prerequisites
*   **Docker Desktop** or **Docker Engine**
*   **Docker Compose**

### 1. Launch the Application
Buka terminal di direktori proyek dan jalankan perintah berikut:
```bash
docker compose up -d
```
> **Note:** Initial setup may take a moment while the MariaDB container imports the `init.sql` file.

### 2. Access the Platform
*   **Main Website**: [http://localhost:8084](http://localhost:8084)
*   **Admin Panel**: [http://localhost:8084/wp-admin/](http://localhost:8084/wp-admin/)
    *   **Username**: `admin`
    *   **Password**: `password_anda` (Gunakan password yang sudah diset sebelumnya)

---

## 🛠 Troubleshooting

*   **Database Connection Error**: Jika muncul pesan error database, tunggu beberapa saat. MariaDB sedang melakukan proses impor data dari `init.sql`. Anda dapat memantau log dengan: `docker compose logs -f db`.
*   **Permission Denied**: Jika tidak bisa upload media atau install plugin, kontainer dikonfigurasi menggunakan user `www-data` (UID 33). Jalankan perintah berikut di host: `sudo chown -R 33:33 wp-content/`.

---

## 👥 Authors

Developed by **Widi Firmaan**.
