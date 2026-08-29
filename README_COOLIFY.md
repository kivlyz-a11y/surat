# Panduan Deployment SIM-SURAT ke Coolify Menggunakan Docker

Proyek **Sistem Manajemen Nomor Surat (SIM-SURAT)** telah dikonfigurasi secara lengkap dengan `Dockerfile`, `docker-compose.yml`, dan *script* inisialisasi otomatis (`docker-entrypoint.sh`) sehingga siap di-*deploy* langsung ke **Coolify** (VPS Linux / Server).

---

## 📁 Berkas Konfigurasi Docker yang Tersedia

1. **`Dockerfile`**: Image PHP 8.3 Apache yang sudah terpasang ekstensi lengkap (`intl`, `zip`, `gd`, `mysqli`, `pdo_mysql`, `mbstring`, `opcache`), Composer 2, dan konfigurasi *DocumentRoot* `/public`.
2. **`docker-compose.yml`**: Konfigurasi multi-container (Aplikasi CI4 + Database MariaDB) lengkap dengan **Persistent Volume** agar data surat dan file lampiran yang diunggah tidak hilang saat container di-*restart* atau di-*redeploy*.
3. **`docker/docker-entrypoint.sh`**: Skrip otomatis yang akan menunggu database siap, menjalankan migrasi tabel (`php spark migrate`), dan mengisi data awal jika database masih kosong (*auto-seeding*).
4. **`.dockerignore`**: Memastikan proses build image di Coolify berjalan cepat dan bersih.
5. **`.env.example`**: Referensi variabel lingkungan (*Environment Variables*).

---

## 🚀 Cara 1: Deploy ke Coolify via Git Repository (Direkomendasikan)

Jika proyek Anda di-push ke GitHub / GitLab:

1. **Buka Dashboard Coolify** Anda.
2. Masuk ke **Projects** $\rightarrow$ Pilih **Environment** (misal: Production) $\rightarrow$ Klik **+ New Resource**.
3. Pilih **Public Repository** atau **Private Repository (GitHub/GitLab App)**.
4. Masukkan URL repository git Anda.
5. Pada pilihan **Build Pack**, pilih salah satu:
   - **Docker Compose** (Jika ingin database MariaDB otomatis dibuatkan di dalam stack yang sama).
   - **Dockerfile** (Jika ingin menggunakan database MySQL terpisah yang sudah ada di Coolify).
6. **Atur Environment Variables** di menu *Environment Variables* Coolify:
   ```env
   CI_ENVIRONMENT=production
   APP_URL=https://surat.domainanda.com/
   DB_HOST=db
   DB_DATABASE=db_nomor_surat
   DB_USER=user_surat
   DB_PASSWORD=surat_secret_password123
   DB_ROOT_PASSWORD=root_secret_password123
   ```
7. Atur domain di bagian **Domains** (misal: `https://surat.domainanda.com`).
8. Klik tombol **Deploy**!

Coolify akan secara otomatis:
- Mem-build container
- Menghubungkan ke database
- Menjalankan migrasi database & seeder
- Menerbitkan sertifikat SSL Let's Encrypt secara otomatis.

---

## 🐳 Cara 2: Deploy Menggunakan Docker Compose Langsung di Coolify

1. Di dashboard Coolify, klik **+ New Resource** $\rightarrow$ **Docker Compose**.
2. Salin seluruh isi berkas `docker-compose.yml` ke dalam editor Coolify.
3. Sesuaikan variabel environment `APP_URL` dengan domain Anda.
4. Klik **Save** & **Deploy**.

---

## 💾 Lokasi Persistent Storage (Volume)

Berkas lampiran surat dan data MySQL disimpan pada Docker Volume persisten:
- **Berkas Surat Upload**: `sim_surat_uploads_data` $\rightarrow$ `/var/www/html/writable/uploads`
- **Basis Data MariaDB**: `sim_surat_db_data` $\rightarrow$ `/var/lib/mysql`

---

## 🔑 Akun Login Default Setelah Deploy

Setelah deployment selesai, Anda dapat langsung login menggunakan:
- **Admin**: `username: admin` | `password: admin123`
- **Pegawai**: `username: pegawai1` | `password: pegawai123`
*(Disarankan untuk langsung mengganti password default melalui menu Profil setelah login pertama kali).*
