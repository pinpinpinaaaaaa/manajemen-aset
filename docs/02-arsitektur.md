# 02 — Arsitektur Sistem

## Diagram Arsitektur (Production)

```mermaid
graph TB
    subgraph Client["Browser / Pengguna"]
        UA["User (Staff Internal)"]
        UB["User Eksternal (Form Publik)"]
    end

    subgraph Docker["Docker Network: simaster"]
        direction TB
        NGX["nginx:1.26\nPort 8000→80\nServe static files\n+ proxy PHP"]
        APP["php:8.2-fpm\nLaravel App\n(Port 9000)"]
        WRK["php:8.2-fpm\nQueue Worker\nqueue:listen"]
        SCH["php:8.2-fpm\nScheduler\nschedule:work"]
        VOL[("storage_data\nNamed Volume\n/var/www/html/storage")]
    end

    subgraph ExternalDB["Database On-Premise / Local"]
        DB[("MySQL 8.0\n(container mysql DEV\natau server ext PROD)")]
    end

    CF["Cloudflare\nZero Trust Tunnel\n(cloudflared binary)"]

    UA -->|HTTP :8000| NGX
    UB -->|HTTP :8000| NGX
    NGX -->|FastCGI :9000| APP
    NGX -->|serve /storage/| VOL
    APP <-->|R/W| VOL
    WRK <-->|R/W| VOL
    APP -->|PDO MySQL| CF
    WRK -->|PDO MySQL| CF
    SCH -->|PDO MySQL| CF
    CF -->|TCP tunnel| DB
```

> **Dev vs Prod:**  
> - **Dev** (`docker-compose.yml`): MySQL berjalan sebagai container `mysql` di network yang sama. `CF_ACCESS_CLIENT_ID` tidak diset → cloudflared tidak dijalankan, `DB_HOST=mysql`.  
> - **Prod** (`docker-compose.prod.yml`): Tidak ada container MySQL. Database di server on-premise. `DB_HOST=127.0.0.1` karena cloudflared membuat TCP proxy lokal.

---

## Komponen & Fungsinya

| Komponen | File / Image | Tanggung Jawab |
|---|---|---|
| **Nginx** | `docker/nginx.conf`, image `nginx-stage` | Menerima request HTTP, serve `public/build/` (assets) dan `storage/` (file upload), forward PHP ke php-fpm |
| **PHP-FPM App** | `Dockerfile` target `runtime` | Menjalankan Laravel — routing, controller, Eloquent, render view |
| **Queue Worker** | Container `worker`, command `queue:listen` | Memproses job asinkron dari tabel `jobs` (database driver) |
| **Scheduler** | Container `scheduler`, command `schedule:work` | Menjalankan cron Laravel — saat ini hanya `rekap:bulanan` tiap tgl 1 jam 00:00 |
| **MySQL** | `mysql:8.0` / server eksternal | Penyimpanan data utama — semua tabel transaksional, session, queue, cache |
| **Named Volume `storage_data`** | Docker volume | Persists file upload (foto aset, struk, dokumen) agar tidak hilang saat image di-rebuild |
| **Cloudflare Zero Trust** | `cloudflared` binary di image | Membuat tunnel terenkripsi dari container ke MySQL on-premise (tanpa expose port DB ke internet) |

---

## Alur Request HTTP

```mermaid
sequenceDiagram
    participant B as Browser
    participant N as Nginx
    participant P as PHP-FPM (Laravel)
    participant M as MySQL

    B->>N: GET /dashboard
    N->>P: FastCGI forward
    P->>P: public/index.php → bootstrap/app.php
    P->>P: Middleware: auth → cek session
    P->>P: Middleware: menu.access → cek RBAC
    P->>M: Query data (Eloquent)
    M-->>P: Result set
    P->>P: View::composer inject $menus
    P->>P: Render Blade template
    P-->>N: HTML response
    N-->>B: HTTP 200
```

---

## Struktur Folder

```
manajemen-aset/
├── app/
│   ├── Console/Commands/     ← Artisan commands (RekapBulanan)
│   ├── Exports/              ← Kelas export Excel (Maatwebsite) & PDF helper per-modul
│   ├── Http/
│   │   ├── Controllers/      ← ~30 controller, 1 file per modul
│   │   └── Middleware/       ← CheckMenuAccess (RBAC)
│   ├── Models/               ← ~57 Eloquent model, semua extend BaseModel
│   ├── Observers/            ← UserObserver
│   ├── Providers/            ← AppServiceProvider (View::composer untuk $menus)
│   ├── Services/             ← MenuAccessService, AuditLogService, AsetLogService
│   └── helpers.php           ← Global helper functions
├── bootstrap/
│   └── app.php               ← Entry config Laravel: routing, middleware alias, schedule
├── config/
│   ├── coa.php               ← Chart of Accounts (kategori anggaran bertingkat)
│   └── *.php                 ← Config standar Laravel (database, queue, cache, dll)
├── database/
│   ├── migrations/           ← ~60 migration file (additive-only di production)
│   ├── seeders/              ← Seeder untuk data awal (role, menu, superadmin)
│   └── database.sqlite       ← Database dev lokal (SQLite)
├── docker/
│   ├── entrypoint.sh         ← Init: cloudflared → storage → migrate → cache → php-fpm
│   └── nginx.conf            ← Konfigurasi Nginx (upload 64MB, cache static, proxy PHP)
├── docs/                     ← Dokumentasi teknis ini
├── public/
│   ├── index.php             ← Entry point web (satu-satunya PHP yang di-hit langsung)
│   └── build/                ← Output Vite (CSS/JS ter-bundle + hash nama file)
├── resources/
│   ├── css/                  ← styles.css (custom) + app.css (Tailwind)
│   ├── js/                   ← app.js + modul JS kustom
│   ├── sass/                 ← SCSS files
│   └── views/                ← Blade templates, 1 subfolder per modul
├── routes/
│   └── web.php               ← Semua route (publik + auth-protected)
├── storage/
│   ├── app/public/           ← File upload (symlink dari public/storage)
│   ├── framework/            ← Cache view, session, compiled routes
│   └── logs/                 ← Laravel log files
├── tests/
│   ├── Feature/              ← Feature tests (ApprovalWorkflowTest)
│   └── Unit/                 ← Unit tests (placeholder)
├── Dockerfile                ← Multi-stage build: assets → runtime → nginx-stage
├── docker-compose.yml        ← Dev: 5 services + MySQL container
└── docker-compose.prod.yml   ← Prod: 4 services, tanpa MySQL (eksternal via CF tunnel)
```

---

## Pola Desain

### MVC + Service Layer

```
HTTP Request
    ↓
Route (routes/web.php)
    ↓
Middleware (auth, menu.access)
    ↓
Controller (app/Http/Controllers/)
    ├── Service calls (MenuAccessService, AuditLogService, AsetLogService)
    ├── Model (app/Models/) — Eloquent ORM
    │     └── BaseModel.booted() → AuditLogService (otomatis setiap CRUD)
    └── View (resources/views/) ← AppServiceProvider inject $menus via View::composer
```

### BaseModel & Audit Otomatis

Semua model (kecuali `AuditLog` sendiri) extend `BaseModel` ([`app/Models/BaseModel.php`](../app/Models/BaseModel.php)). `BaseModel::booted()` mendaftarkan Eloquent event listener untuk `created`, `updated`, `deleted` yang otomatis memanggil `AuditLogService::log()`.

```mermaid
graph LR
    A[Model::create/update/delete] --> B[BaseModel Eloquent Event]
    B --> C[AuditLogService::log]
    C --> D[(audit_logs table)]
```

### RBAC (Role-Based Access Control)

```mermaid
graph TD
    U[User] -->|id_role| R[Role]
    R -->|menu JSON array| M[Menu IDs]
    U -->|menu JSON override| MO[Menu IDs override]
    
    MS[MenuAccessService] -->|1. user.menu != null| MO
    MS -->|2. role.menu != null| M
    MS -->|3. keduanya null| FA[Full Access]
    
    CMA[CheckMenuAccess Middleware] --> MS
    CMA -->|cek route_name di tabel menus| DB[(menus table)]
    CMA -->|abort 403| DENY[Akses ditolak]
    CMA -->|pass| CTRL[Controller]
```

**Hirarki akses** (`app/Services/MenuAccessService.php`):
1. Jika `user.menu != null` → pakai override individual
2. Jika `role.menu != null` → pakai menu dari role
3. Jika keduanya `null` → **full access** (khusus superadmin tanpa pembatasan)

Middleware `menu.access` di-alias di `bootstrap/app.php` → `App\Http\Middleware\CheckMenuAccess`.

---

## Modul-Modul Utama

| Modul | Controller | Model Utama |
|---|---|---|
| **Infrastruktur** | `GedungController`, `RuanganController` | `Gedung`, `Ruangan`, `GedungGambar`, `GedungDenah`, `RuanganGambar` |
| **Aset Inventaris** | `AsetController` | `Aset`, `AsetLog`, `JenisBarang` |
| **APAR** | `AparController` | `Apar`, `AparLog` |
| **Kendaraan** | `KendaraanController` | `Kendaraan` |
| **Gudang** | `GudangController` | `GudangBarang`, `GudangTransaksi`, `GudangTransaksiDetail`, `GudangTransaksiCharge`, `GudangRekapBulanan`, `GudangOpnameHeader`, `GudangOpnameDetail` |
| **Peminjaman** | `PeminjamanAsetController`, `PeminjamanRuanganController` | `PeminjamanAset`, `PeminjamanAsetDetail`, `PeminjamanRuangan`, `PeminjamanRuanganDetail`, `PeminjamanRuanganAset`, `PeminjamanRuanganKonsumsi` |
| **Permintaan & Pengadaan** | `PermintaanBarangGudangController`, `PengadaanBarangJasaController` | `PermintaanBarangGudang`, `PengadaanBarangJasa`, `PengadaanBarangJasaDetail`, `PengadaanBarangJasaFile` |
| **Kendaraan (Permintaan)** | `PermintaanKendaraanController` | `PermintaanKendaraan`, `PermintaanKendaraanDetail`, `PermintaanKendaraanItem` |
| **Pemindahan** | `PemindahanAsetController` | `PemindahanAset`, `PemindahanAsetDetail` |
| **Ekspedisi** | `EkspedisiController` | `Ekspedisi`, `EkspedisiBarang`, `EkspedisiDokumen`, `EkspedisiPengiriman` |
| **Pengaduan Kerusakan** | `PengaduanKerusakanController` | `PengaduanKerusakan`, `PengaduanKerusakanDetail` |
| **Maintenance** | `MaintenanceController` | `Maintenance`, `MaintenanceDetail` |
| **Pemusnahan** | `LaporanPemusnahanController` | `LaporanPemusnahan` |
| **Laporan Tahunan** | `LaporanTahunanController` | `LaporanTahunan`, `LaporanTahunanDetail`, `LaporanTahunanSummary` |
| **Anggaran RKAT** | `AnggaranController` | `RkatAnggaran`, `RkatRealisasi` |
| **Admin** | `UserController`, `RolesController`, `MenuController` | `User`, `Role`, `Menu` |
| **Audit** | `AuditLogController` | `AuditLog` |
| **Vendor** | `VendorController` | `Vendor` |
