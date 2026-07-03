# Sistem Informasi Manajemen Aset

Aplikasi web berbasis **Laravel 12** untuk mengelola aset, infrastruktur, gudang, dan berbagai kegiatan operasional organisasi. Dikembangkan selama program magang sebagai sistem manajemen aset terpadu yang mencakup siklus penuh aset — dari pengadaan, pemakaian, pemindahan, maintenance, hingga pemusnahan.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Bootstrap 5, Tailwind CSS 4, Vite 7 |
| Database | MySQL 8.0 (production) · SQLite (development) |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel 3.1 |
| Image | intervention/image 3 |
| Containerization | Docker, Docker Compose |
| DB Tunnel | Cloudflare Zero Trust (cloudflared) |
| Queue & Schedule | Laravel Queue (database driver) |

---

## Fitur Utama

### Manajemen Infrastruktur
- **Gedung & Ruangan** — CRUD gedung dan ruangan, upload foto carousel, denah layout, status aktif/maintenance

### Manajemen Aset (3 Kategori)
- **Aset Inventaris** — peralatan kantor, elektronik, furnitur
- **APAR** — alat pemadam kebakaran dengan tracking inspeksi
- **Kendaraan** — armada kendaraan dinas dengan histori pemakaian

Setiap aset memiliki log perubahan status otomatis (AuditLog) dan log aktivitas naratif manual (AsetLog).

### Gudang
- Stok barang dengan transaksi masuk/keluar
- Stok opname
- **Rekap bulanan otomatis** — dijadwalkan tiap tanggal 1 jam 00:00 via Laravel Scheduler

### Workflow Peminjaman
- **Peminjaman Aset** — form publik (tanpa login) → approval admin → serahkan → kembalikan
- **Peminjaman Ruangan** — form publik → approval → pengelolaan jadwal
- Pengecekan ketersediaan real-time via AJAX dengan `lockForUpdate()` untuk mencegah race condition

### Pengadaan & Permintaan
- **Permintaan Barang dari Gudang** — potong stok otomatis saat disetujui
- **Pengadaan Barang & Jasa** — manajemen pembelian ke vendor eksternal
- **Manajemen Vendor** — database vendor dengan form registrasi publik

### Operasional
- **Permintaan Kendaraan** — booking armada dengan cek ketersediaan
- **Pemindahan Aset** — relokasi aset antar ruangan/gedung dengan histori
- **Ekspedisi** — pengiriman barang keluar, cetak label & tanda terima
- **Pengaduan Kerusakan** — form laporan publik untuk pihak eksternal

### Maintenance & Laporan
- **Maintenance** — jadwal perbaikan aset, tracking progres
- **Laporan Pemusnahan** — dokumentasi pemusnahan aset
- **Laporan Tahunan** — rekap aset per tahun, export Excel & PDF

### Admin & Keamanan
- **Manajemen User & Role** — CRUD user dengan role-based access
- **RBAC berbasis Menu** — tiap role/user punya daftar menu yang boleh diakses; dikontrol via middleware `CheckMenuAccess`
- **Audit Log** — setiap perubahan data (create/update/delete) dicatat otomatis via Eloquent events di `BaseModel`

---

## Arsitektur

```
app/
├── Http/
│   ├── Controllers/     # ~30 controller, 1 per fitur
│   └── Middleware/      # CheckMenuAccess — RBAC berbasis menu
├── Models/              # ~57 Eloquent model, semua extend BaseModel
├── Services/            # MenuAccessService, AuditLogService, AsetLogService
├── Exports/             # Export Excel (Maatwebsite) per modul
└── Console/Commands/    # RekapBulanan — artisan command terjadwal
```

### Alur Request
```
Request HTTP
  → public/index.php
  → bootstrap/app.php  (routing, middleware alias, schedule)
  → Middleware: auth + CheckMenuAccess (RBAC)
  → Controller
  → Model (Eloquent)
  → BaseModel::booted() otomatis catat ke AuditLog
  → View (Blade + Vite assets)
```

### Sistem RBAC
```
User → id_role → Role.menu (JSON array menu IDs)
               ↑ bisa di-override per user dengan User.menu

MenuAccessService::allowedMenuIds():
  1. Pakai User.menu jika ada (override individual)
  2. Pakai Role.menu jika ada
  3. null = full access (superadmin)
```

### Workflow Approval (semua modul transaksional)
```
[Pengaju] → decision_status: "menunggu_persetujuan"
[Admin]   → approve() / reject()
          → (jika disetujui) proses → status: "Sedang Diproses"
          → selesai → status: "Selesai"
```

### Format Primary Key
Hampir semua tabel memakai string PK dengan format `PREFIX-YYYYMMDD-NNNN`:
```
PJM-20260101-0001  (Peminjaman Aset)
PMR-20260101-0001  (Peminjaman Ruangan)
EKS-20260101-0001  (Ekspedisi)
```

---

## Menjalankan di Development

### Prasyarat
- PHP 8.2+
- Composer
- Node.js 18+
- (Opsional) MySQL 8.0 — default pakai SQLite

### Setup Pertama Kali

```bash
# Clone repo
git clone https://github.com/USERNAME/manajemen-aset.git
cd manajemen-aset

# Install semua dependensi, copy .env, generate key, migrate, build assets
composer run setup
```

### Jalankan Dev Server

```bash
# Menjalankan semuanya sekaligus: PHP server + queue worker + log tail + Vite HMR
composer run dev
```

Buka http://localhost:8000 — login dengan akun seeder (cek `database/seeders/`).

### Perintah Lain

```bash
# Jalankan semua test
composer run test

# Jalankan satu test spesifik
php artisan test --filter NamaTest

# Format/lint PHP
./vendor/bin/pint

# Trigger rekap bulanan gudang secara manual
php artisan rekap:bulanan

# Build aset frontend untuk production
npm run build
```

---

## Deploy ke Production (Docker + VPS)

Project sudah dilengkapi Dockerfile multi-stage dan dua file Docker Compose:
- `docker-compose.yml` — untuk development lokal (include MySQL)
- `docker-compose.prod.yml` — untuk production (database eksternal via Cloudflare Tunnel)

### Yang Dijalankan Docker
| Service | Fungsi |
|---|---|
| `app` | PHP-FPM 8.2 |
| `nginx` | Web server, serve static files |
| `worker` | `queue:listen` — proses background job |
| `scheduler` | `schedule:work` — rekap bulanan otomatis |

### Setup `.env` di Server

```bash
cp .env.example .env
# Edit .env dan isi nilai berikut:
```

```ini
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...          # generate: php artisan key:generate --show
APP_URL=https://domain-kamu.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1           # 127.0.0.1 karena diakses via cloudflared tunnel
DB_PORT=3306
DB_DATABASE=management-aset
DB_USERNAME=...
DB_PASSWORD=...

# Cloudflare Zero Trust — untuk tunnel ke database on-premise
CF_ACCESS_HOSTNAME=...
CF_ACCESS_CLIENT_ID=...
CF_ACCESS_CLIENT_SECRET=...
```

### Deploy

```bash
# Install Docker CE (CentOS/RHEL)
yum install -y yum-utils
yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
yum install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
systemctl enable --now docker

# Build & jalankan
docker compose -f docker-compose.prod.yml up -d --build

# Seed database (hanya deployment pertama)
docker compose -f docker-compose.prod.yml exec app php artisan db:seed
```

---

## Struktur Direktori Penting

```
manajemen-aset/
├── app/
│   ├── Http/Controllers/    # Controller per modul
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic layer
│   ├── Exports/             # Kelas export Excel
│   └── helpers.php          # Global helper functions
├── database/
│   ├── migrations/          # Skema database
│   └── seeders/             # Data awal (user, role, menu)
├── resources/
│   ├── views/               # Blade templates (1 folder per modul)
│   ├── css/                 # Styling (Bootstrap + Tailwind)
│   └── js/                  # JavaScript
├── routes/
│   └── web.php              # Semua route (publik + auth-protected)
├── docker/
│   ├── entrypoint.sh        # Container startup script
│   └── nginx.conf           # Nginx config
├── Dockerfile               # Multi-stage build (Node → PHP-FPM → Nginx)
├── docker-compose.yml       # Development (+ MySQL lokal)
└── docker-compose.prod.yml  # Production (DB eksternal via CF tunnel)
```

---

## Form Publik (Tanpa Login)

Beberapa fitur dapat diakses tanpa autentikasi, untuk pihak eksternal:

| URL | Fungsi |
|---|---|
| `/form-peminjaman-ruangan` | Pengajuan peminjaman ruangan |
| `/form_peminjaman_aset` | Pengajuan peminjaman aset |
| `/form-permintaan-kendaraan` | Booking kendaraan dinas |
| `/form-pengaduan-kerusakan` | Laporan kerusakan |
| `/form-ekspedisi` | Permintaan ekspedisi |
| `/form-pengadaan-barang` | Form pengadaan barang |
| `/form-permintaan-barang` | Permintaan barang dari gudang |
| `/vendor/create` | Registrasi vendor baru |

---

## Lisensi

Dikembangkan untuk keperluan internal organisasi. Tidak untuk distribusi komersial.
