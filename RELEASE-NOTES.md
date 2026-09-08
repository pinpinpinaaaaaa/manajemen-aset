# Catatan Rilis — Sistem Manajemen Aset (SIMASTER)

**Versi:** Update Pertama pasca-deploy awal  
**Tanggal:** September 2026  
**Dari commit:** `3c90588` (initial commit)  
**Hingga commit:** `beed7d3` (HEAD, main)  
**Jumlah commit baru:** 20 commit

---

## ⚠️ LANGKAH DEPLOY (BACA DULU SEBELUM LANJUT)

> **Ini bukan fresh install.** Ikuti checklist ini **secara berurutan** setelah `git pull`.
> **JANGAN jalankan `migrate:fresh`, `db:seed` (DatabaseSeeder), atau `db:wipe` — akan menghapus semua data produksi.**

### Checklist Deploy Update

```
[ ] 1. git pull
[ ] 2. composer install --no-dev (jika ada package baru)
[ ] 3. npm ci && npm run build (rebuild asset frontend)
[ ] 4. php artisan migrate          ← WAJIB (ada 1 migrasi baru)
[ ] 5. php artisan db:seed --class=AddGudangMenusSeeder  ← WAJIB (menu gudang baru)
[ ] 6. Upload file PDF manual       ← WAJIB (taruh manual di server)
[ ] 7. php artisan config:clear && php artisan view:clear && php artisan route:clear && php artisan cache:clear
[ ] 8. (restart queue worker jika pakai supervisor)
```

---

### Detail Tiap Langkah

#### Langkah 4 — Migrasi Database (WAJIB)

Ada **1 migrasi baru** yang harus dijalankan:

```bash
php artisan migrate
```

**Migrasi:** `2026_09_01_152443_fix_sessions_rename_id_user_to_user_id`  
**Efek:** Rename kolom `id_user` → `user_id` di tabel `sessions`.  
**Kenapa penting:** Tanpa migrasi ini, semua user mendapat **error 500 saat login**. Migrasi ini sudah diperbaiki di versi baru dan aman dijalankan pada data aktif (ALTER TABLE biasa, tidak membuang data).

> Cek status migrasi dulu jika ragu:
> ```bash
> php artisan migrate:status
> ```
> Migrasi `fix_sessions_rename_id_user_to_user_id` harus berstatus **Pending** sebelum dijalankan.

---

#### Langkah 5 — Jalankan Seeder Aditif (WAJIB)

```bash
php artisan db:seed --class=AddGudangMenusSeeder
```

**Efek:** Menambahkan 2 menu baru ke tabel `menus`:
- **Transaksi Gudang** (`gudang.transaksi.index`)
- **Stok Opname** (`gudang.stok_opname.index`)

**Kenapa aman:** Seeder ini menggunakan `firstOrCreate` — tidak akan membuat duplikat jika dijalankan dua kali. Tidak menyentuh menu lain atau assignment role yang sudah ada.

Setelah seeder jalan, **assign menu ini ke role yang perlu akses gudang** via halaman Admin → Roles.

---

#### Langkah 6 — Upload File Manual Pengguna (WAJIB)

File PDF manual **tidak ikut push ke git** (ukuran besar, konten statis). Taruh secara manual:

```bash
# Di server, pastikan folder sudah ada:
mkdir -p public/manual

# Upload file via scp/sftp dari mesin lokal:
scp "Manual-SIMASTER.pdf" user@server:/opt/manajemen-aset/public/manual/
```

Atau jika pakai Docker:
```bash
docker cp "Manual-SIMASTER.pdf" app:/var/www/html/public/manual/
```

File harus bisa diakses di URL: `https://domain-kamu/manual/Manual-SIMASTER.pdf`

Sidebar akan menampilkan link "Manual Pengguna" yang mengarah ke file ini.

---

#### Jika pakai Docker

```bash
cd /opt/manajemen-aset
git pull
docker compose -f docker-compose.prod.yml up -d --build
# Build otomatis menjalankan composer install + npm build

# Setelah container jalan:
docker compose -f docker-compose.prod.yml exec app php artisan migrate
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class=AddGudangMenusSeeder
docker compose -f docker-compose.prod.yml exec app php artisan config:clear
docker compose -f docker-compose.prod.yml exec app php artisan view:clear
docker compose -f docker-compose.prod.yml exec app php artisan route:clear

# Upload file manual:
docker cp "Manual-SIMASTER.pdf" app:/var/www/html/public/manual/
```

---

## Ringkasan Perubahan

---

### 1. Perbaikan Alur Inti

#### Peminjaman Aset — Tombol Serahkan & Kembalikan
**Sebelumnya:** Halaman detail peminjaman aset menampilkan `...` di kolom Aksi. Admin tidak bisa menyerahkan atau menerima kembali aset dari antarmuka — alur peminjaman stuck setelah disetujui.

**Sekarang:** Kolom Aksi menampilkan tombol kondisional sesuai status tiap item:
- Status `menunggu` atau `disetujui` → tombol **Serahkan**
- Status `dipinjam` → tombol **Kembalikan** (buka modal konfirmasi)
- Status `dikembalikan` → badge Dikembalikan (read-only)

Alur peminjaman aset sekarang bisa diselesaikan end-to-end dari UI.

---

#### Pengaduan Kerusakan → Maintenance (3 perbaikan sekaligus)
**Sebelumnya:** Menyetujui (approve) pengaduan kerusakan tidak membuat record `MaintenanceDetail`. Akibatnya:
- Tiket tidak muncul di halaman Maintenance Berjalan
- Aset yang dilaporkan rusak tidak berubah status/kelayakan
- Tidak ada riwayat aktivitas (AsetLog) untuk transisi ini

**Sekarang:**
- `approve()` membuat `MaintenanceDetail` per aset yang dilaporkan
- Status aset otomatis berubah ke `maintenance`, kelayakan ke `4 (Perlu Perbaikan)`
- AsetLog `maintenance_baru` dicatat otomatis (konsisten dengan jalur manual)
- Tiket muncul di Maintenance Berjalan

---

### 2. Perbaikan Bug Fungsional

#### Modal Pemindahan Aset di Dashboard Ruangan — SELALU GAGAL
**Sebelumnya:** Modal "Pindahkan Aset" di dashboard ruangan selalu gagal saat submit karena field name tidak cocok antara form dan controller:
- Form mengirim `details[n][id_gedung]` tapi controller mengharapkan `to_gedung[]`

**Sekarang:** Field name disesuaikan dengan ekspektasi controller. Field alasan dijadikan 1 input global (bukan per-aset). JS toggle enable/disable select tujuan saat checkbox aset dipilih.

---

#### Modal Maintenance di Dashboard Ruangan — Aset Tak Dicentang Ikut Submit
**Sebelumnya:** Hidden input `id_aset` berada di luar div yang di-toggle, sehingga seluruh aset (termasuk yang tidak dicentang) selalu ikut terkirim. Aset yang tidak dicentang tidak punya data kerusakan → validasi `required` gagal.

**Sekarang:** Hidden input dipindah ke dalam `detail-form` div. Semua input (id_aset, kerusakan, foto_before) diberi `disabled` awal; hanya ter-enable saat checkbox dicentang → tidak ikut submit jika tidak dicentang.

---

#### Form Publik — Endpoint AJAX Tidak Bisa Diakses
**Sebelumnya:** 3 endpoint AJAX berada di dalam grup middleware `auth`, sehingga form publik (tanpa login) tidak bisa memanggil:
- `/get-ruangan-available` — dropdown ruangan di form peminjaman ruangan
- `/get-aset-tersedia` — filter aset di form pengaduan kerusakan
- `/cek-ketersediaan` — cek kuota unit tersedia di form peminjaman aset

**Sekarang:** Ketiga endpoint dipindah ke luar grup `auth` (bersifat read-only, tidak mutasi data). Form publik berfungsi normal.

---

#### Konsumsi Peminjaman Ruangan — Jumlah Bisa Kosong Diam-diam
**Sebelumnya:** Checkbox konsumsi (air mineral, makanan ringan, makanan berat) tidak punya atribut `name`. Server tidak bisa membedakan "tidak dicentang" vs "dicentang tapi jumlah dikosongkan". Kondisi lama (`empty(jumlah) → skip`) diam-diam membuang konsumsi yang diminta user.

**Sekarang:** Tambah `name="konsumsi[n][dipilih]"` ke tiap checkbox. Controller memvalidasi: jika `dipilih=1` tapi jumlah kosong, kembalikan error spesifik per jenis (Bahasa Indonesia).

---

#### Catatan Penolakan Permintaan Kendaraan Tidak Tersimpan
**Sebelumnya:** Method `reject()` tidak menerima parameter `Request`, sehingga catatan yang diisi admin di modal Tolak tidak terbaca dan tidak tersimpan ke database.

**Sekarang:** `reject()` menerima `Request $request`, memvalidasi field `catatan` (wajib diisi, max 1000 karakter), dan menyimpan catatan bersama status `ditolak`. Halaman detail menampilkan label "Alasan Penolakan" saat status ditolak.

---

#### Badge Status "Tersedia" di Permintaan Barang Selalu Abu-abu
**Sebelumnya:** Kondisi `match` memeriksa string `'Sudah Tersedia'`, padahal nilai aktual dari database adalah `'Tersedia'`. Badge jatuh ke default (warna abu-abu) untuk semua permintaan yang sudah tersedia.

**Sekarang:** String disesuaikan dengan nilai enum aktual. Badge "Tersedia" tampil dengan warna biru (info).

---

### 3. Perbaikan RBAC & Sidebar

**Sebelumnya:** Beberapa menu tidak muncul di sidebar meskipun user punya akses:
- 5 menu dengan nama route mengandung `-` (dash) tidak terbaca karena helper memeriksa `_` (underscore)
- Guard section Maintenance dan Sarana salah kondisi

**Sekarang:**
- Perbaiki `canMenu()` untuk menangani route dengan dash maupun underscore
- Fix guard section Maintenance dan Sarana di sidebar
- Tambah link **Manual Pengguna** di bagian bawah sidebar (mengarah ke file PDF)

---

### 4. Perbaikan Gudang

#### Menu Transaksi Gudang & Stok Opname Tidak Muncul
**Sebelumnya:** Menu Transaksi Gudang dan Stok Opname tidak pernah didaftarkan ke tabel `menus` saat deploy awal, sehingga tidak muncul di sidebar dan tidak bisa diakses via RBAC.

**Sekarang:** `AddGudangMenusSeeder` mendaftarkan kedua menu secara aditif (tanpa menghapus data). Lihat Langkah 5 di atas.

---

#### Satuan Barang Gudang Selalu Error Validasi
**Sebelumnya:** Barang dengan satuan tunggal (misal: `lembar/lembar`) selalu gagal disimpan karena `konversi_satuan` dan `satuan_dasar` dianggap wajib oleh validasi.

**Sekarang:** Kolom `konversi_satuan` dan `satuan_dasar` dijadikan nullable. Form menampilkan checkbox "Barang ini punya satuan konversi" — jika tidak dicentang, satuan dasar otomatis diisi sama dengan satuan utama (konversi = 1:1).

---

#### Layout Form Transaksi Gudang Rusak
**Sebelumnya:** CSS global (`styles.css`) men-set `display:flex` dan `min-width:300px` pada `.form-select` sehingga kolom satuan di form transaksi meluap ke area qty dan tata letak hancur.

**Sekarang:** Override CSS spesifik di halaman transaksi gudang untuk mengembalikan layout yang benar.

---

### 5. Perbaikan Form (Error Display & Validasi)

#### 6 Form Publik — Tidak Ada Pesan Error & Data Hilang Setelah Submit Gagal
Sebelumnya, jika form publik gagal validasi server-side (misalnya format email salah), user diarahkan ke halaman kosong atau data yang sudah diisi hilang semua.

**Sekarang** — semua 6 form publik sudah diperbaiki:

| Form | Perbaikan Kritis |
|---|---|
| Peminjaman Aset | TomSelect restore + item loop |
| Peminjaman Ruangan | **Bug kritis:** nama field `peserta_rapat` ↔ `nama_kegiatan` tertukar → data tersimpan ke kolom salah. **Sudah diperbaiki.** |
| Pengaduan Kerusakan | Cascading gedung→ruangan→aset restore async |
| Permintaan Barang | TomSelect restore + item loop |
| Pengadaan Barang | Item restore per jenis (barang/jasa) |
| Permintaan Kendaraan | Auto-trigger cek ketersediaan setelah restore |

Semua controller mendapat custom messages validasi Bahasa Indonesia.

---

#### 10 Form Internal (Edit) — Tidak Ada Pesan Error & Data Reset Ke DB
Sebelumnya, jika edit form gagal validasi, user tidak tahu field mana yang salah dan semua perubahan yang belum disimpan hilang (tampil nilai lama dari DB).

**Sekarang** — 10 form internal edit sudah diperbaiki dengan:
- Kotak error ringkasan di atas form (`<x-form-errors />`)
- Highlight merah per field yang salah (`is-invalid` + pesan spesifik)
- Nilai form kembali ke yang terakhir diketik (bukan reset ke DB)

Form yang diperbaiki: `maintenance/edit`, `ekspedisi/edit`, `peminjaman_aset/edit`, `pemindahan_aset/edit`, `laporan_pemusnahan/edit`, `pengadaan_barang/edit`, `permintaan_barang/edit`, `peminjaman_ruangan/edit`, `roles/create`, `roles/edit`, `gudang/create`, `gudang/edit`, `vendor/create`.

**Bug fungsional yang ditemukan & diperbaiki selama proses ini:**
- `peminjaman_aset/edit`: Selector JS `.item-row` tidak ada di HTML → AJAX cek ketersediaan tidak pernah terpicu
- `vendor/create`: Selector JS `.npwp-file` / `.pakta_integritas-file` tidak ada (div pakai `data-file=...` bukan `class=...`) → toggle radio Upload File/Link tidak berfungsi untuk 2 dokumen tersebut

---

### 6. Perbaikan Tampilan

#### Layout PDF
- Refactor jadi 2 tipe: **Tipe A** (laporan biasa, logo di atas) dan **Tipe B** (surat resmi, kop surat + footer per halaman)
- Fix posisi logo yang geser di beberapa ekspor PDF
- Fix `colspan` pada tabel kosong di 7 halaman (pengadaan, ekspedisi, peminjaman, dll)

#### Halaman Detail Aset
- Kode aset (misal `A0025`) kini jadi judul utama besar — mudah dibaca saat membuka halaman
- Nama aset jadi sub-judul di bawahnya

---

### 7. Fitur Baru

| Fitur | Deskripsi |
|---|---|
| **Manual Pengguna** | Link ke PDF panduan penggunaan di sidebar (bawah menu) |
| **Tombol Pindahkan Semua** | Di halaman detail pemindahan aset: pindahkan semua aset yang belum dipindah sekaligus (bulk), dengan konfirmasi eksplisit dan guard anti-duplikat AsetLog |
| **Export PDF Lengkap** | Di laporan tahunan: tombol baru "Export PDF Lengkap" menghasilkan PDF semua section (pengadaan, gudang, maintenance, pemusnahan) dalam satu file. Tombol lama sekarang berlabel "Export PDF (Tab Aktif)" |

---

## Pemeriksaan Keamanan Sebelum Push

| Pemeriksaan | Status | Catatan |
|---|---|---|
| `.env` tidak ter-commit | ✅ Aman | Ada di `.gitignore` |
| `database/database.sqlite` tidak ter-commit | ✅ Aman | Ada di `database/.gitignore` (`*.sqlite*`) |
| File upload tidak ter-commit | ✅ Aman | Folder `storage/app/public/*/` ada di `.gitignore` |
| File `MANUAL PENGGUNA.docx` / `Manual-SIMASTER.pdf` | ✅ Aman | Untracked, upload manual ke server |
| Tidak ada file debug/test tidak sengaja ter-commit | ✅ Aman | Cek via `git show --stat HEAD` tiap commit |
| `.env.example` ter-commit | ✅ Sengaja | Template konfigurasi, tidak berisi nilai sensitif |

---

## Daftar File Berubah (Ringkasan)

| Kategori | File Utama yang Berubah |
|---|---|
| Controller | `PengaduanKerusakanController`, `PeminjamanRuanganController`, `PemindahanAsetController`, `PermintaanKendaraanController`, `GudangController`, + 5 controller form publik |
| View (form) | 13 form internal edit + 6 form publik + `ruangan/dashboard.blade.php` |
| View (laporan/show) | `peminjaman_aset/show`, `pemindahan_aset/show`, `laporan_tahunan/show` |
| Database | 1 migrasi baru (`fix_sessions`), 1 seeder baru (`AddGudangMenusSeeder`) |
| Layout/Partial | `sidebar.blade.php`, `pdf/layout.blade.php`, `pdf/layout-surat.blade.php` (baru) |
| Routes | `routes/web.php` (pindah 3 endpoint ke publik, hapus 5 dead route) |
| Komponen Baru | `resources/views/components/form-errors.blade.php` |
| Infrastruktur | `DEPLOYMENT.md` (baru), `docker-compose.prod.yml` (diperbarui), `.env.example` (diperbarui) |
