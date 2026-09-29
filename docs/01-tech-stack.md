# 01 — Teknologi & Spesifikasi

## Bahasa & Framework

| Komponen | Versi | Kegunaan |
|---|---|---|
| PHP | ^8.2 | Runtime backend |
| Laravel | ^12.0 | Framework MVC utama |
| Node.js | 22 (Alpine, build stage) | Build frontend assets (Vite) |

---

## Library PHP (Production)

| Library | Versi | Kegunaan |
|---|---|---|
| `laravel/framework` | ^12.0 | Core framework, routing, Eloquent ORM, queue, scheduler |
| `barryvdh/laravel-dompdf` | ^3.1 | Generate PDF (laporan, label ekspedisi, tanda terima) |
| `maatwebsite/excel` | ^3.1 | Export/import Excel (.xlsx) — Maatwebsite Excel berbasis PhpSpreadsheet |
| `intervention/image` | ^3.11 | Resize & proses gambar upload (foto aset, denah gedung) |
| `laravel/ui` | ^4.6 | Auth scaffolding (login, register, middleware auth) |
| `laravel/tinker` | ^2.10.1 | REPL untuk debugging di environment lokal/staging |

## Library PHP (Development)

| Library | Versi | Kegunaan |
|---|---|---|
| `phpunit/phpunit` | ^11.5.3 | Unit & feature testing |
| `laravel/pint` | ^1.24 | Code formatter (PHP-CS-Fixer wrapper) |
| `laravel/pail` | ^1.2.2 | Log tailing real-time di terminal |
| `laravel/sail` | ^1.41 | Dev environment berbasis Docker (opsional, tidak dipakai di production) |
| `fakerphp/faker` | ^1.23 | Generate data dummy untuk factories/seeders |
| `nunomaduro/collision` | ^8.6 | Error reporting lebih informatif di CLI |
| `mockery/mockery` | ^1.6 | Mocking objects di unit test |

---

## Library Frontend

| Library | Versi | Kegunaan |
|---|---|---|
| Vite | ^7.0.7 | Module bundler & dev server — bundle CSS/JS ke `public/build/` |
| `laravel-vite-plugin` | ^2.0.0 | Integrasi Vite dengan Laravel (hot reload, manifest) |
| Bootstrap | ^5.2.3 | Komponen UI (grid, modal, badge, btn, alert, table) |
| `@popperjs/core` | ^2.11.6 | Dependency Bootstrap (tooltip, dropdown) |
| Tailwind CSS | ^4.0.0 | Utility classes tambahan di samping Bootstrap |
| `@tailwindcss/vite` | ^4.0.0 | Plugin Vite untuk Tailwind v4 |
| Sass | ^1.56.1 | Pre-processor CSS (`resources/sass/`) |
| Axios | ^1.11.0 | HTTP client JavaScript (AJAX cek ketersediaan, dll) |
| Prettier | ^3.8.1 + `prettier-plugin-blade` | Code formatting Blade templates |

---

## Database & Storage

| Komponen | Versi | Kegunaan |
|---|---|---|
| MySQL | 8.0 (Docker image) | Database utama production |
| SQLite | (bawaan PHP) | Alternatif untuk development lokal (`database/database.sqlite`) |
| File storage lokal | — | Upload foto/dokumen di `storage/app/public/` — **tidak** menggunakan cloud storage |

**Driver Laravel yang dipakai:**

```
SESSION_DRIVER=database     → tabel sessions
QUEUE_CONNECTION=database   → tabel jobs, failed_jobs
CACHE_STORE=database        → tabel cache
```

---

## Infrastruktur & Deploy

| Komponen | Image / Teknologi | Fungsi |
|---|---|---|
| PHP-FPM | `php:8.2-fpm` | Proses PHP — container `app` |
| Nginx | `nginx:1.26-alpine` | Web server + static file serving — container `nginx` |
| MySQL | `mysql:8.0` | Database — container `mysql` (dev) / eksternal (prod) |
| Queue Worker | image `app` | Proses queue database — container `worker` |
| Scheduler | image `app` | Jalankan cron `rekap:bulanan` — container `scheduler` |
| Docker | Engine ≥ 24 + Compose plugin | Orkestrasi semua container |
| Cloudflare Zero Trust | `cloudflared` (binary di image) | Tunnel terenkripsi ke database on-premise (production) |

**Docker build stages** (`Dockerfile`):
1. `assets` (Node 22 Alpine) — `npm run build` → `public/build/`
2. `runtime` (PHP 8.2-FPM) — install Composer deps + salin kode + hasil Vite
3. `nginx-stage` (Nginx 1.26 Alpine) — serve `public/` + proxy ke PHP-FPM

**PHP extensions yang diinstall di image:**
`pdo_mysql`, `gd`, `zip`, `bcmath`, `intl`, `mbstring`, `xml`, `dom`, `exif`, `opcache`

---

## Kebutuhan Server

| Resource | Minimum (Estimasi) | Catatan |
|---|---|---|
| CPU | 2 core | Scheduler + worker + php-fpm + nginx berjalan bersamaan |
| RAM | 2 GB | PHP memory_limit=256M per request; queue worker, scheduler, nginx, mysql overhead |
| Disk | 20 GB | Kode ~500MB; MySQL data; storage file upload (foto aset, dokumen) |
| OS | Linux (amd64) | Image Docker berbasis Alpine/Debian |
| Docker | ≥ 24 | Dengan Compose plugin v2 |
| Port terbuka | 8000 (atau `APP_PORT`) | Nginx mendengar di port ini |

> **[PERLU KONFIRMASI]** Spesifikasi server production aktual (CPU, RAM, disk, OS, provider).

---

## Tools Build, Test & Format

| Perintah | Tool | Fungsi |
|---|---|---|
| `npm run build` | Vite | Bundle CSS/JS untuk production |
| `npm run dev` | Vite dev server | Hot reload saat development |
| `composer run test` | PHPUnit ^11 | Jalankan semua test |
| `php artisan test --filter NamaTest` | PHPUnit | Jalankan satu test spesifik |
| `./vendor/bin/pint` | Laravel Pint | Format/lint kode PHP |
| `composer run dev` | concurrently | Jalankan server + queue + log + vite bersamaan (dev) |

---

## Layanan Pihak Ketiga

| Layanan | Kegunaan | Konfigurasi |
|---|---|---|
| Cloudflare Zero Trust | Tunnel terenkripsi dari container ke database MySQL on-premise | `CF_ACCESS_HOSTNAME`, `CF_ACCESS_CLIENT_ID`, `CF_ACCESS_CLIENT_SECRET` di `.env` |
| GitHub Container Registry (`ghcr.io`) | Opsional: hosting Docker image untuk deployment produksi | `GITHUB_USER` di `.env`; hanya jika pakai `docker-compose.prod.yml` mode [A] |
| Email (SMTP) | Opsional — default `MAIL_MAILER=log` (tidak kirim email) | `MAIL_*` di `.env` — **[PERLU KONFIRMASI]** apakah email aktif di production |
