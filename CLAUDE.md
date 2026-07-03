# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Commands

```bash
# Setup pertama kali: install deps, copy .env, generate key, migrate, build assets
composer run setup

# Jalankan dev environment lengkap (server + queue + log tail + vite, semua serentak)
composer run dev

# Jalankan semua test
composer run test

# Jalankan satu test spesifik
php artisan test --filter NamaTest

# Format/lint PHP
./vendor/bin/pint

# Build frontend assets untuk production
npm run build

# Jalankan migrasi database
php artisan migrate

# Rekap stok bulanan gudang (biasanya jalan otomatis tiap tanggal 1 jam 00:00)
php artisan rekap:bulanan
```

Database default: **SQLite** (`database/database.sqlite`). Ganti ke MySQL dengan uncomment `DB_*` di `.env`. Queue dan session pakai driver database — wajib migrate dulu sebelum jalankan queue worker.

---

## 1. Arsitektur Umum

Project ini adalah **Sistem Manajemen Aset** berbasis **Laravel 12**, menggunakan pola **MVC + Service Layer**.

```
app/
├── Http/
│   ├── Controllers/     ← Logika per-fitur (~30 controller)
│   └── Middleware/      ← CheckMenuAccess (RBAC berbasis menu)
├── Models/              ← Eloquent models (~57 model), semua extend BaseModel
├── Services/            ← MenuAccessService, AuditLogService, AsetLogService
├── Exports/             ← Kelas export Excel (Maatwebsite) per-modul
├── Console/Commands/    ← RekapBulanan (artisan command)
├── Observers/           ← UserObserver
└── helpers.php          ← Global helper functions

resources/views/         ← Blade templates, 1 subfolder per modul
routes/web.php           ← Semua route (publik + auth-protected)
bootstrap/app.php        ← Entry konfigurasi Laravel (middleware alias, schedule)
```

**Modul-modul utama dan tanggung jawabnya:**

| Modul | Controller | Tanggung Jawab |
|---|---|---|
| Infrastruktur | `GedungController`, `RuanganController` | CRUD gedung & ruangan; gambar, denah |
| Aset Inventaris | `AsetController`, `AparController`, `KendaraanController` | CRUD 3 kategori aset + log perubahan |
| Gudang | `GudangController` | Stok barang, transaksi masuk/keluar, stok opname, rekap bulanan |
| Peminjaman | `PeminjamanAsetController`, `PeminjamanRuanganController` | Workflow pinjam-kembali aset & ruangan |
| Permintaan & Pengadaan | `PermintaanBarangGudangController`, `PengadaanBarangJasaController` | Permintaan barang dari gudang & pembelian ke vendor |
| Kendaraan | `PermintaanKendaraanController` | Booking armada kendaraan |
| Pemindahan | `PemindahanAsetController` | Relokasi aset antar ruangan/gedung |
| Ekspedisi | `EkspedisiController` | Pengiriman barang keluar, cetak label & tanda terima |
| Pengaduan | `PengaduanKerusakanController` | Laporan kerusakan dari eksternal |
| Maintenance | `MaintenanceController` | Jadwal & track perbaikan aset |
| Laporan | `LaporanPemusnahanController`, `LaporanTahunanController` | Laporan pemusnahan aset & rekap tahunan |
| Admin | `RolesController`, `UserController`, `MenuController` | Manajemen user, role, dan akses menu |

---

## 2. Entry Point — Apa yang Terjadi Saat Aplikasi Start

```
Browser/Client
     ↓ HTTP Request
public/index.php                ← Web root, satu-satunya file PHP yang di-hit langsung
     ↓
bootstrap/app.php               ← Konfigurasi inti aplikasi Laravel:
     │  ├── withRouting()       → mendaftarkan routes/web.php
     │  ├── withMiddleware()    → mendaftarkan alias 'menu.access' = CheckMenuAccess
     │  └── withSchedule()     → mendaftarkan schedule: rekap:bulanan tiap tgl 1
     ↓
app/Providers/AppServiceProvider.php  ← Boot() dijalankan setelah app siap:
     │  └── View::composer('*')  → untuk SETIAP view yang di-render,
     │                             inject $menus (daftar menu user yang login)
     │                             via MenuAccessService::menusForUser()
     ↓
routes/web.php                  ← Cocokkan URL dengan controller
```

Singkatnya: setiap request PHP masuk lewat `public/index.php`, Laravel bootstrap dirinya, lalu cocokkan URL ke route yang sesuai.

---

## 3. Alur Data — Dari Request Sampai Response

### 3a. Request Biasa (Halaman Dashboard)

```
User buka /dashboard
     ↓
routes/web.php: Route::get('/dashboard', [DashboardController::class, 'index'])
  → Middleware group: ['auth', 'menu.access']
     ↓
Middleware 'auth' (Laravel built-in)
  → Cek apakah user sudah login. Jika belum → redirect ke /login
     ↓
Middleware 'menu.access' = CheckMenuAccess
  → Ambil nama route saat ini ('dashboard')
  → Cari di tabel menus: ada Menu dengan route_name='dashboard'?
  → Jika ada, cek apakah menu.id itu ada di daftar allowedMenuIds(user)
  → Jika tidak punya akses → abort(403)
     ↓
DashboardController::index()
  → Query ke berbagai Model (Aset, Kendaraan, Maintenance, dll.)
  → Kumpulkan semua data statistik
  → return view('dashboard', compact(...))
     ↓
AppServiceProvider (View::composer)
  → Inject $menus ke view sebelum render
     ↓
resources/views/dashboard.blade.php
  → Extends layouts/app.blade.php
  → layouts/app.blade.php include: navbar.blade.php, sidebar.blade.php, footer.blade.php
  → Sidebar menggunakan $menus untuk render navigasi sesuai akses user
     ↓
HTML Response dikirim ke browser
```

### 3b. Request Form Publik (Tanpa Login)

Beberapa fitur bisa diakses tanpa login (untuk pihak eksternal):
- `/form-permintaan-kendaraan`, `/form-ekspedisi`, `/form-pengaduan-kerusakan`
- `/form-pengadaan-barang`, `/form-permintaan-barang`
- `/form-peminjaman-ruangan`, `/form_peminjaman_aset`
- `/vendor/create`

```
User eksternal buka /form-peminjaman-ruangan
     ↓
routes/web.php: route ini di LUAR middleware group 'auth'
     ↓
PeminjamanRuanganController::create()
  → Tidak cek auth sama sekali
  → return view('peminjaman_ruangan.form', ...)
     ↓
User isi form → POST /form-peminjaman-ruangan
     ↓
PeminjamanRuanganController::store()
  → Validasi input
  → DB::transaction() { buat record PeminjamanRuangan + detail }
  → decision_status diset 'menunggu_persetujuan'
  → redirect back dengan flash message success
```

### 3c. Alur Approval Workflow (contoh: Peminjaman Aset)

Setiap modul transaksional punya alur yang sama persis:

```
[Pengaju] → store() → decision_status = 'menunggu_persetujuan'
                            ↓
[Admin/Staff login] → melihat di index() → list item pending
                            ↓
                    approve() → decision_status = 'disetujui'
                    reject()  → decision_status = 'ditolak'
                            ↓ (jika disetujui)
                    serahkanItem() → aset.status = 'terpakai'
                                   → detail.status_pengembalian = 'dipinjam'
                                   → AsetLog::create() ← MANUAL log aktivitas aset
                            ↓
                    kembalikan() → aset.status = 'tersedia'
                                 → detail.status_pengembalian = 'dikembalikan'
                                 → AsetLog::create() ← MANUAL log aktivitas aset
                                 → Jika semua item kembali → parent.status = 'Selesai'
```

**Dua status field yang selalu ada di tabel transaksional:**
- `decision_status`: `menunggu_persetujuan` → `disetujui` / `ditolak`
- `status`: `Belum Diproses` → `Sedang Diproses` → `Selesai`

### 3d. Alur Penyimpanan Data ke Database (via Model)

```
Controller memanggil Model::create() atau $model->update()
     ↓
Eloquent menyimpan ke database
     ↓
BaseModel::booted() EVENT LISTENER terpicu otomatis:
  → created  → AuditLogService::log('create', table, id, null, newData)
  → updated  → AuditLogService::log('update', table, id, oldData, changes)
               (skip kalau cuma updated_at yang berubah)
  → deleted  → AuditLogService::log('delete', table, id, oldData, null)
     ↓
AuditLogService::log()
  → AuditLog::create({
      id_user, action, table_name, record_id,
      old_data (JSON), new_data (JSON),
      ip_address, user_agent
    })
```

Catatan: `AuditLog` model sendiri **tidak** extend `BaseModel` (untuk menghindari infinite loop).

### 3e. Export Data

```
User klik "Export PDF" / "Export Excel"
     ↓
Controller::exportPdfRiwayat() / exportExcelRiwayat()
     ↓
PDF:   Pdf::loadView('modul.pdf', $data)->setPaper('A4')->download('nama.pdf')
Excel: Excel::download(new ModulExport($params), 'nama.xlsx')
     ↓
PDF view ada di: resources/views/modul/pdf.blade.php
Excel class ada di: app/Exports/ModulExport.php
```

---

## 4. Hubungan Antar File — Siapa Panggil Siapa

### Rantai Dependency Utama

```
public/index.php
  └── bootstrap/app.php
        ├── routes/web.php
        │     └── App\Http\Controllers\*Controller
        │           ├── App\Models\* (extends BaseModel atau Model)
        │           │     └── (jika BaseModel) App\Services\AuditLogService
        │           │                               └── App\Models\AuditLog
        │           ├── App\Exports\*Export  ← untuk Excel
        │           └── Barryvdh\DomPDF\Facade\Pdf ← untuk PDF
        ├── App\Http\Middleware\CheckMenuAccess
        │     └── App\Services\MenuAccessService
        │           └── App\Models\Menu
        └── App\Providers\AppServiceProvider
              └── App\Services\MenuAccessService
                    └── App\Models\Menu, App\Models\User
```

### Dependency Model-ke-Model (contoh: Aset)

```
Aset (app/Models/Aset.php)
  ├── belongsTo Gedung
  ├── belongsTo Ruangan
  ├── belongsTo JenisBarang
  ├── hasMany MaintenanceDetail
  ├── hasMany PemindahanAsetDetail
  ├── hasMany PeminjamanAsetDetail
  ├── hasMany PeminjamanRuanganAset
  ├── hasMany LaporanPemusnahan
  └── hasMany AsetLog
```

### Sistem RBAC — Hirarki Akses

```
User
  ├── id_role → Role
  │                └── menu (JSON array of menu IDs)  ← akses default dari role
  └── menu (JSON array of menu IDs)                   ← override per-user (opsional)

MenuAccessService::allowedMenuIds(user):
  1. Jika user.menu != null → pakai user.menu (override individual)
  2. Jika user.role.menu != null → pakai role.menu
  3. Jika keduanya null → return null = FULL ACCESS (admin)

CheckMenuAccess middleware:
  1. Jika allowedMenuIds() == null → langsung lolos (full access)
  2. Cari Menu record dengan route_name = route saat ini
  3. Jika tidak ada record Menu → langsung lolos (route non-menu)
  4. Jika ada → cek apakah menu.id ada di allowed list → jika tidak: abort(403)
```

---

## 5. Hal Penting Lain

### Custom Primary Key

Hampir semua model memakai **string PK** dengan format `PREFIX-YYYYMMDD-NNNN`, dibuat di controller masing-masing:

```php
// Contoh dari PeminjamanAsetController
private function generateId(): string {
    $date = now()->format('Ymd');
    $last = PeminjamanAset::where('id_peminjaman', 'like', "PJM-$date-%")
        ->orderByDesc('id_peminjaman')->first();
    $next = $last ? (int) substr($last->id_peminjaman, -4) + 1 : 1;
    return "PJM-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
}
```

Prefix per modul: `PJM` (peminjaman aset), `PMR` (peminjaman ruangan), `EKS` (ekspedisi), dst. Selalu cari method `generateId()` di controller yang bersangkutan.

### AsetLog vs AuditLog — Dua Sistem Log Berbeda

| | AuditLog | AsetLog |
|---|---|---|
| **Siapa** | Semua model yang extend BaseModel | Khusus aset (Aset, Apar) |
| **Kapan** | Otomatis via Eloquent events | Manual, dipanggil eksplisit di controller |
| **Isi** | Before/after JSON dari field yang berubah | Event naratif: "dipinjam", "dikembalikan", "dicek", dll. |
| **File** | `app/Services/AuditLogService.php` | Controller masing-masing memanggil `AsetLog::create()` |

### Rekap Bulanan Gudang (Scheduled Task)

`RekapBulanan` artisan command dijadwalkan otomatis tiap tanggal 1 jam 00:00 (dikonfigurasi di `bootstrap/app.php`). Command ini memanggil `GudangController::rekapBulan()` yang menyalin `stok_akhir` bulan lalu ke tabel `gudang_rekap_bulanan`. Bisa juga dipicu manual: `php artisan rekap:bulanan`.

### Cek Ketersediaan Real-time (AJAX)

Beberapa form punya endpoint AJAX untuk cek ketersediaan sebelum submit:
- `/cek-ketersediaan` → `PeminjamanAsetController::cekKetersediaan()` — hitung unit tersedia di rentang tanggal
- `/cek-ketersediaan-ruangan` → `PeminjamanRuanganController::cekKetersediaanRuangan()`
- `/cek-kendaraan` → `PermintaanKendaraanController::cekKetersediaan()`

Semua pakai `DB::transaction()` + `lockForUpdate()` saat store untuk menghindari race condition.

### Frontend

Layout utama: `resources/views/layouts/app.blade.php`. Semua halaman protected extend layout ini. Bootstrap 5 (CDN) untuk komponen UI, Tailwind CSS 4 (Vite) untuk utility classes. Assets di-bundle Vite dari `resources/css/` dan `resources/js/app.js`.

### Transaksi Database

Semua operasi yang menyentuh lebih dari satu tabel dibungkus `DB::transaction()`, termasuk: store peminjaman, update pemindahan, kembalikan aset, proses permintaan barang (yang memotong stok gudang).
