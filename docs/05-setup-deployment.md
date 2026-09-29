# 05 — Setup & Deployment

## Setup Lokal (Development)

### Prasyarat

- PHP ≥ 8.2 (dengan ekstensi: `pdo_mysql`, `gd`, `zip`, `bcmath`, `intl`, `mbstring`, `xml`, `dom`)
- Composer ≥ 2.x
- Node.js ≥ 22.x + npm
- SQLite (bawaan PHP) atau MySQL 8.0 (opsional)

### Langkah Setup Pertama Kali

```bash
# 1. Clone repo
git clone https://github.com/pinpinpinaaaaaa/manajemen-aset.git
cd manajemen-aset

# 2. Jalankan setup lengkap (install deps + env + key + migrate + build assets)
composer run setup
# Ekuivalen dengan:
#   composer install
#   cp .env.example .env
#   php artisan key:generate
#   php artisan migrate
#   npm install && npm run build

# 3. Buat symlink storage (untuk akses file upload via browser)
php artisan storage:link

# 4. Jalankan dev environment (server + queue + log tail + vite)
composer run dev
```

Aplikasi berjalan di: `http://localhost:8000`

### Akun Default (dari Seeder)

Seed data awal dijalankan dengan:
```bash
php artisan db:seed
# PERHATIAN: db:seed berbeda dari db:seed DatabaseSeeder — seeder aman, tidak menghapus data
```

> **JANGAN** jalankan `migrate:fresh`, `db:wipe`, atau `migrate:fresh --seed` di environment yang sudah ada data — ini akan **menghapus semua data**.

---

## Environment Variables (`.env`)

| Variable | Default | Keterangan |
|---|---|---|
| `APP_NAME` | `"Manajemen Aset"` | Nama aplikasi, muncul di tab browser |
| `APP_ENV` | `local` | `local` / `production` — affects error display |
| `APP_KEY` | (generated) | Encryption key — **JANGAN share / commit** |
| `APP_DEBUG` | `true` | **Set `false` di production** |
| `APP_URL` | `http://localhost:8000` | URL publik aplikasi |
| `APP_PORT` | `8000` | Port Nginx (Docker) |
| `DB_CONNECTION` | `sqlite` | `sqlite` atau `mysql` |
| `DB_HOST` | `127.0.0.1` | Host MySQL (`mysql` untuk Docker dev, `127.0.0.1` untuk prod via CF tunnel) |
| `DB_PORT` | `3306` | Port MySQL |
| `DB_DATABASE` | `manajemen_aset` | Nama database |
| `DB_USERNAME` | `root` | Username database |
| `DB_PASSWORD` | — | Password database — **JANGAN commit** |
| `SESSION_DRIVER` | `database` | Driver session — wajib migrate sebelum pakai |
| `QUEUE_CONNECTION` | `database` | Driver queue |
| `CACHE_STORE` | `database` | Driver cache |
| `MAIL_MAILER` | `log` | `log` = tidak kirim email; set `smtp` untuk email aktif |
| `MAIL_HOST` | — | SMTP host (jika `MAIL_MAILER=smtp`) |
| `MAIL_PORT` | — | SMTP port |
| `MAIL_USERNAME` | — | SMTP user |
| `MAIL_PASSWORD` | — | SMTP password |
| `CF_ACCESS_HOSTNAME` | — | Hostname Cloudflare tunnel ke database (prod) |
| `CF_ACCESS_CLIENT_ID` | — | Client ID Cloudflare Zero Trust (prod) |
| `CF_ACCESS_CLIENT_SECRET` | — | Client Secret Cloudflare Zero Trust (prod) |
| `GITHUB_USER` | — | Username GitHub untuk pull image dari GHCR (opsional, prod) |

---

## Docker (Development)

### Struktur Container

```
docker-compose.yml (dev):
  ├── app       — PHP-FPM (Laravel app)
  ├── nginx     — Nginx web server (port 8000:80)
  ├── mysql     — MySQL 8.0 (port 3306:3306)
  ├── worker    — Queue worker (queue:listen)
  └── scheduler — Scheduler (schedule:work)
```

### Perintah Docker Dev

```bash
# Jalankan semua container
docker compose up -d

# Lihat log
docker compose logs -f app
docker compose logs -f worker

# Masuk ke container app
docker compose exec app bash

# Jalankan artisan dari luar container
docker compose exec app php artisan migrate
docker compose exec app php artisan rekap:bulanan

# Stop semua container
docker compose down
```

### Build Image

```bash
# Build image dari Dockerfile (multi-stage)
docker build -t manajemen-aset:latest .

# Build untuk production (tanpa dev deps)
docker build --target nginx-stage -t manajemen-aset-nginx:latest .
```

---

## Docker (Production)

### Struktur Container Prod

```
docker-compose.prod.yml:
  ├── app       — PHP-FPM (image dari GHCR atau build lokal)
  ├── nginx     — Nginx
  ├── worker    — Queue worker
  └── scheduler — Scheduler
  # Tidak ada container MySQL — DB di server on-premise via Cloudflare tunnel
```

### Deploy ke Production

```bash
# Pull image terbaru
docker compose -f docker-compose.prod.yml pull

# Jalankan (recreate jika image berubah)
docker compose -f docker-compose.prod.yml up -d --force-recreate

# Lihat status
docker compose -f docker-compose.prod.yml ps
```

### Entrypoint Container (`docker/entrypoint.sh`)

Saat container `app` start, script ini dijalankan secara berurutan:

```
1. Cek CF_ACCESS_CLIENT_ID → jika ada, jalankan cloudflared (tunnel ke DB)
2. Tunggu koneksi database siap (retry tiap 2 detik)
3. php artisan storage:link
4. php artisan migrate --force (jalankan migration baru)
5. php artisan config:cache
6. php artisan route:cache
7. php artisan view:cache
8. php-fpm
```

> **Penting**: `migrate --force` dijalankan otomatis saat container start di production. Pastikan migration baru bersifat **additive** (tidak DROP TABLE atau DROP COLUMN) sebelum deploy.

---

## Cloudflare Zero Trust Tunnel

Dipakai di **production** untuk koneksi dari Docker container ke database MySQL on-premise.

### Cara Kerja

```
Container app/worker/scheduler
    ↓ connect ke DB_HOST=127.0.0.1:3306
cloudflared (berjalan di background dalam container)
    ↓ TCP proxy tunnel
Cloudflare Zero Trust Network
    ↓ meneruskan ke
MySQL on-premise server
```

### Setup Cloudflare Tunnel

1. Login ke Cloudflare Zero Trust dashboard
2. Buat **Application** bertipe TCP
3. Buat **Service Token** → dapat `CF_ACCESS_CLIENT_ID` dan `CF_ACCESS_CLIENT_SECRET`
4. Atur **hostname** untuk tunnel → `CF_ACCESS_HOSTNAME`
5. Set ketiga variable tersebut di `.env` production

### Konfigurasi `.env` untuk Prod

```env
CF_ACCESS_HOSTNAME=db.yourdomain.com
CF_ACCESS_CLIENT_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.access
CF_ACCESS_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
DB_HOST=127.0.0.1
DB_PORT=3306
```

> Jika `CF_ACCESS_CLIENT_ID` tidak diset di `.env`, cloudflared **tidak dijalankan** dan koneksi DB langsung ke `DB_HOST` (cocok untuk dev dengan MySQL container lokal).

---

## Backup & Restore

> **[PERLU KONFIRMASI]** Saat ini **tidak ada prosedur backup otomatis** yang terkonfigurasi. Ini adalah risiko operasional — lihat [`07-pertanyaan-terbuka.md`](07-pertanyaan-terbuka.md).

### Backup Manual Database

```bash
# Backup dari container MySQL (dev)
docker compose exec mysql mysqldump -u root -p manajemen_aset > backup_$(date +%Y%m%d).sql

# Backup dari server MySQL eksternal (prod — jalankan dari server DB)
mysqldump -u USERNAME -p manajemen_aset > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Backup File Upload (storage/)

```bash
# Dari host Docker, backup named volume
docker run --rm \
  -v manajemen-aset_storage_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/storage_backup_$(date +%Y%m%d).tar.gz /data
```

### Restore Database

```bash
# Restore ke MySQL (hati-hati: ini menimpa data yang ada)
mysql -u USERNAME -p manajemen_aset < backup_20260901.sql

# Atau dari container:
docker compose exec -T mysql mysql -u root -p manajemen_aset < backup_20260901.sql
```

### Restore File Upload

```bash
# Extract backup ke volume
docker run --rm \
  -v manajemen-aset_storage_data:/data \
  -v $(pwd):/backup \
  alpine tar xzf /backup/storage_backup_20260901.tar.gz -C /
```

### Rekomendasi Backup Otomatis

Jadwalkan di cron server (bukan di dalam Laravel scheduler karena scheduler ada di container):

```bash
# /etc/cron.d/manajemen-aset-backup
0 2 * * * root mysqldump -u backup_user -pPASSWORD manajemen_aset | gzip > /backups/db_$(date +\%Y\%m\%d).sql.gz
# Simpan 30 hari terakhir:
0 3 * * * root find /backups -name "db_*.sql.gz" -mtime +30 -delete
```

---

## Migration Policy

**Wajib**: semua migration bersifat **additive-only**:

| Boleh (`up()`) | JANGAN (`up()`) |
|---|---|
| `Schema::create(...)` | `Schema::dropIfExists(...)` |
| `$table->addColumn(...)` | `$table->dropColumn(...)` |
| `$table->index(...)` | `Schema::drop(...)` |
| `$table->foreign(...)` | Modify column type yang bisa truncate data |

Jika perlu hapus kolom/tabel lama, tandai dulu dengan `nullable()` → deploy → hapus di migration berikutnya setelah dipastikan tidak ada data yang bergantung.

Jalankan migration:
```bash
# Lokal
php artisan migrate

# Production (otomatis saat container start, atau manual):
docker compose exec app php artisan migrate --force
```
