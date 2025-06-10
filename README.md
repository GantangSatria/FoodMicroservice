# 🍱 kITA BAYAR - Backend Microservices

**kITA BAYAR** adalah sistem backend berbasis microservice untuk platform pemesanan makanan online, terintegrasi dengan payment gateway **Midtrans**. Proyek ini dibangun untuk memberikan pengalaman pemesanan makanan yang seamless dan hemat biaya, serta memudahkan integrasi layanan restoran.


<a href="#developer">
  <img src="https://img.shields.io/badge/Lihat%20Developer-blue?style=for-the-badge" alt="tim pengembang">
</a>

---

## 🏗️ Arsitektur Microservice

![Arsitektur kITA BAYAR](https://drive.google.com/uc?export=view&id=15HGwJJj6ucYkjfFIOeC61J0gDGG4tORU)

---

## 🗃️ Diagram Tabel

![Diagram Tabel](https://drive.google.com/uc?export=view&id=1aTQv4YGmulSGNnCf5FpinFt6kDViNpeL)

---


# Panduan Setup Backend KitaBayar
Panduan ini akan membantu Anda mengatur dan menjalankan aplikasi backend KitaBayar menggunakan Docker.

## Prasyarat
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) terinstal dan berjalan
- Git terinstal di sistem Anda
- Akses ke API key yang diperlukan (App Key, Midtrans Key)

## Langkah-langkah Setup

### 1. Persiapkan Docker Desktop
Pastikan Docker Desktop terinstal dan berjalan di sistem Anda. Anda dapat memverifikasi Docker berfungsi dengan menjalankan:
```bash
docker --version
docker-compose --version
```

### 2. Clone Repository
Clone repository backend ke mesin lokal Anda:
```bash
git clone https://github.com/GantangSatria/FoodMicroservice
```

### 3. Navigasi ke Direktori Proyek
```bash
cd kitabayar-be
```

### 4. Update ke Kode Terbaru
Pastikan Anda memiliki versi terbaru dari kode:
```bash
git pull origin main
```

### 5. Konfigurasi Environment
Salin template environment ke setiap direktori service:
```bash
# Salin template .env ke setiap service
cp .env.example .env
# Atau jika Anda memiliki template di setiap folder service:
cp service1/.env.example service1/.env
cp service2/.env.example service2/.env
```

### 6. Konfigurasi Variabel Environment
Sistem KitaBayar menggunakan arsitektur microservice dengan 5 service terpisah. Setiap service memiliki file `.env` sendiri yang perlu dikonfigurasi.

#### Generate Key yang Diperlukan
Sebelum mengkonfigurasi, Anda perlu generate beberapa key:

**Untuk APP_KEY (Laravel/Lumen):**
```bash
# Generate APP_KEY menggunakan artisan (jika tersedia)
php artisan key:generate

# Atau generate manual dengan base64
echo 'base64:'.base64_encode(random_bytes(32))

# Atau gunakan online generator (untuk development saja)
# https://generate-random.org/laravel-key-generator
```

**Untuk JWT_SECRET:**
Gunakan [Djecrety](https://djecrety.ir/) untuk generate JWT secret key yang aman.

#### Konfigurasi Setiap Service

**1. Auth Service (.env)**
```env
APP_NAME=Lumen
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost/
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=mysql-auth
DB_PORT=3306
DB_DATABASE=auth_service_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

JWT_SECRET=your_jwt_secret_from_djecrety
USER_SERVICE_URL=http://user-service:8003/
```

**2. User Service (.env)**
```env
APP_NAME=Lumen
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost/
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=mysql-user
DB_PORT=3306
DB_DATABASE=user_service_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

AUTH_SERVICE_URL=http://auth-service:8004/
```

**3. Restaurant Service (.env)**
```env
APP_NAME=Lumen
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost/
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=mysql-restaurant
DB_PORT=3306
DB_DATABASE=restaurant_service_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

**4. Order Service (.env)**
```env
APP_NAME=Lumen
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost/
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=mysql-order
DB_PORT=3306
DB_DATABASE=order_service_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

**5. Payment Service (.env)**
```env
APP_NAME=Lumen
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost/
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=mysql-payment
DB_PORT=3306
DB_DATABASE=payment_service_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MIDTRANS_SERVER_KEY=your_midtrans_server_key_here
MIDTRANS_IS_PRODUCTION=false

ORDER_SERVICE_URL=http://order-service:8001/
```

#### Langkah-langkah Konfigurasi:

1. **Masuk ke direktori setiap service:**
```bash
# Contoh untuk auth-service
cd auth-service
cp .env.example .env
nano .env  # atau code .env
```

2. **Generate APP_KEY untuk semua service:**
```bash
# Jalankan di setiap direktori service
php artisan key:generate
```

3. **Generate JWT_SECRET:**
   - Kunjungi [https://djecrety.ir/](https://djecrety.ir/)
   - Copy key yang dihasilkan
   - Paste ke `JWT_SECRET` di auth-service

4. **Dapatkan Midtrans Keys:**
   
   **Cara Mendapatkan Midtrans Server Key:**
   - Daftar/Login ke [Midtrans Dashboard](https://dashboard.midtrans.com/)
   - Pilih environment **Sandbox** untuk development atau **Production** untuk live
   - Di dashboard, klik menu **Settings** → **Access Keys**
   - Copy **Server Key** dan **Client Key**
   - Paste Server Key ke `MIDTRANS_SERVER_KEY` di payment-service .env
   
   **Untuk panduan lengkap Midtrans:**
   - [Dokumentasi Midtrans - Getting Started](https://docs.midtrans.com/en/midtrans-account/overview)
   - [Panduan Access Keys](https://docs.midtrans.com/en/midtrans-account/overview#retrieving-api-access-keys)

### 7. Build dan Jalankan dengan Docker Compose
Build dan jalankan semua service:
```bash
docker-compose up --build
```

Untuk menjalankan dalam mode detached (latar belakang):
```bash
docker-compose up --build -d
```

### 8. Verifikasi Instalasi
Periksa apakah semua container berjalan:
```bash
docker-compose ps
```

## Integrasi Frontend
Untuk mengakses aplikasi lengkap, Anda juga perlu setup frontend:

**Repository Frontend:** [KitaBayar Frontend](https://github.com/AndreasBagasgoro/kitabayar-fe)

```bash
# Di terminal/direktori terpisah
git clone https://github.com/AndreasBagasgoro/kitabayar-fe.git
cd kitabayar-fe
# Ikuti instruksi setup frontend
```

## Endpoint API
Setelah backend berjalan, Anda dapat mengakses API di setiap service:

- **Payment Service:** `http://localhost:8000` (Gateway utama)
- **Order Service:** `http://localhost:8001`
- **Restaurant Service:** `http://localhost:8002`
- **User Service:** `http://localhost:8003`
- **Auth Service:** `http://localhost:8004`

Dokumentasi API biasanya tersedia di `/api/documentation` pada setiap service.

## Troubleshooting

### Masalah Umum

**Container Docker tidak mau start:**
```bash
# Periksa log Docker
docker-compose logs
# Restart containers
docker-compose down
docker-compose up --build
```

**Variabel environment tidak termuat:**
```bash
# Verifikasi file .env ada dan memiliki format yang benar
ls -la .env
cat .env
```

**Masalah koneksi database:**
```bash
# Periksa status container database
docker-compose logs db
# Reset database
docker-compose down -v
docker-compose up --build
```

## Menghentikan Aplikasi
Untuk menghentikan semua service:
```bash
# Hentikan containers
docker-compose down
# Hentikan dan hapus volumes (data database akan hilang)
docker-compose down -v
```

## Perintah Development
```bash
# Lihat logs
docker-compose logs -f

# Eksekusi perintah di container yang berjalan
docker-compose exec app bash

# Rebuild service tertentu
docker-compose build app

# Jalankan migrasi database
docker-compose exec app php artisan migrate
```

## Catatan Penting
- Pastikan port 8000-8004 tidak digunakan oleh aplikasi lain
- Simpan API key Midtrans dengan aman dan jangan commit ke repository
- Gunakan JWT secret yang kuat dari [Djecrety](https://djecrety.ir/) untuk keamanan
- Setiap service memiliki APP_KEY yang berbeda untuk keamanan
- Gunakan environment variables yang berbeda untuk production
- Backup database secara berkala jika menggunakan untuk development jangka panjang
- Pastikan semua service dapat berkomunikasi satu sama lain melalui Docker network

## Tool yang Digunakan
- **[Djecrety](https://djecrety.ir/)** - Generator JWT secret key yang aman
- **[Midtrans](https://midtrans.com/)** - Payment gateway Indonesia
- **Docker & Docker Compose** - Containerization
- **MySQL** - Database untuk setiap service
- **Lumen** - Micro-framework PHP untuk microservices

## Arsitektur Microservice
KitaBayar menggunakan arsitektur microservice dengan 5 service utama:

1. **Auth Service** (Port 8004) - Mengelola autentikasi dan JWT
2. **User Service** (Port 8003) - Mengelola data pengguna  
3. **Restaurant Service** (Port 8002) - Mengelola data restoran dan menu
4. **Order Service** (Port 8001) - Mengelola pesanan
5. **Payment Service** (Port 8000) - Mengelola pembayaran dengan Midtrans

Setiap service memiliki database MySQL terpisah dan berkomunikasi melalui HTTP API.


## Developer

| Nama Developer | NIM |
|---|---|
| **Gantang Satria Yudha** | 235150701111036 |
| **Andreas Bagasgoro** | 235150701111037 |
| **Rayyif Rasya** | 235150700111032 |

---
