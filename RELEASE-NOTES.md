# Catatan Rilis — SIMASTER V1 → V2

**Versi:** V2 — Update Pertama Pasca-Deploy  
**Tanggal:** September 2026  
**Titik awal V1:** `af5dab2` (commit terakhir yang sudah ter-deploy)  
**HEAD V2:** `d767ffa` (main)  
**Jumlah commit baru:** 21 commit  

---

## BAGIAN A — Apa yang Berubah dari V1 ke V2

### 1. Bug Kritis — Alur yang Tidak Bisa Diselesaikan

#### Peminjaman Aset — Tombol Serahkan & Kembalikan
**V1:** Kolom Aksi di halaman detail peminjaman menampilkan `...`. Admin tidak bisa menyerahkan atau menerima kembali aset dari UI — alur stuck setelah disetujui.  
**V2:** Kolom Aksi menampilkan tombol kondisional sesuai status tiap item:
- `menunggu` / `disetujui` → tombol **Serahkan**
- `dipinjam` → tombol **Kembalikan** (buka modal konfirmasi)
- `dikembalikan` → badge read-only

Alur peminjaman aset sekarang bisa diselesaikan end-to-end dari UI.

---

#### Pengaduan Kerusakan → Maintenance
**V1:** Menyetujui pengaduan kerusakan tidak membuat record maintenance. Akibatnya: tiket tidak muncul di Maintenance Berjalan, aset tidak berubah status/kelayakan, tidak ada AsetLog.  
**V2:** `approve()` membuat `MaintenanceDetail` per aset, mengubah status aset ke `maintenance` dan kelayakan ke `4 (Perlu Perbaikan)`, mencatat AsetLog `maintenance_baru`. Tiket muncul di Maintenance Berjalan.

---

#### Modal Pemindahan Aset di Dashboard Ruangan
**V1:** Modal "Pindahkan Aset" selalu gagal saat submit — form mengirim `details[n][id_gedung]` tapi controller mengharapkan `to_gedung[]`. Tidak ada pesan error yang jelas.  
**V2:** Field name disesuaikan. Alasan dijadikan 1 input global. JS toggle enable/disable select tujuan saat checkbox aset dipilih.

---

#### Modal Maintenance di Dashboard Ruangan
**V1:** Hidden input `id_aset` berada di luar div toggle → seluruh aset (termasuk yang tidak dicentang) selalu ikut submit → validasi gagal karena aset tidak dicentang tidak punya data kerusakan.  
**V2:** Hidden input dipindah ke dalam `detail-form` div. Input diberi `disabled` awal; ter-enable hanya saat checkbox dicentang.

---

#### Form Publik — Endpoint AJAX Tidak Bisa Diakses
**V1:** 3 endpoint AJAX (`/get-ruangan-available`, `/get-aset-tersedia`, `/cek-ketersediaan`) berada di dalam grup middleware `auth` → form publik mendapat 401, dropdown cascading dan cek ketersediaan tidak berfungsi.  
**V2:** Ketiga endpoint dipindah ke luar grup `auth` (bersifat read-only). Form publik berfungsi normal.

---

### 2. RBAC & Sidebar

**V1:** Beberapa menu tidak muncul di sidebar meski user punya akses:
- 5 menu dengan route name mengandung `-` (dash) tidak terbaca karena helper hanya memeriksa `_` (underscore)
- Guard section Maintenance dan Sarana salah kondisi
- Tidak ada akses ke Manual Pengguna

**V2:**
- `canMenu()` diperbaiki untuk menangani route dengan dash maupun underscore
- Guard section Maintenance dan Sarana difix
- Tambah link **Manual Pengguna** di bagian bawah sidebar (mengarah ke file PDF)

---

### 3. Gudang

**V1 → V2:**

| Masalah V1 | Status V2 |
|---|---|
| Menu Transaksi Gudang & Stok Opname tidak muncul di sidebar | Menu didaftarkan via `AddGudangMenusSeeder` (aditif, tidak hapus data) |
| Barang dengan satuan tunggal (`lembar/lembar`) selalu error validasi | `konversi_satuan` & `satuan_dasar` dijadikan nullable. Checkbox konversi → jika tidak dicentang, satuan dasar = satuan utama |
| Layout form transaksi gudang rusak (kolom satuan meluap ke area qty) | Override CSS spesifik di halaman transaksi gudang |

---

### 4. Form — Error Display & Validasi

**V1:** Jika form (publik maupun internal) gagal validasi server-side:
- Tidak ada indikasi field mana yang salah
- Data yang sudah diisi hilang / kembali ke nilai DB lama

**V2 — 19 form diperbaiki:**

**6 Form Publik** kini menampilkan pesan error Bahasa Indonesia + restore nilai terakhir diketik:

| Form | Catatan Khusus |
|---|---|
| Peminjaman Aset | TomSelect restore + item loop |
| Peminjaman Ruangan | **Bug kritis diperbaiki:** field `peserta_rapat` ↔ `nama_kegiatan` tertukar — data selama ini tersimpan ke kolom yang salah |
| Pengaduan Kerusakan | Cascading gedung→ruangan→aset restore async |
| Permintaan Barang | TomSelect restore + item loop |
| Pengadaan Barang | Item restore per jenis (barang/jasa) |
| Permintaan Kendaraan | Auto-trigger cek ketersediaan setelah restore |

**13 Form Internal Edit** kini menampilkan kotak error ringkasan (`<x-form-errors />`), highlight merah per field, dan nilai terakhir diketik dikembalikan:  
`maintenance/edit`, `ekspedisi/edit`, `peminjaman_aset/edit`, `pemindahan_aset/edit`, `laporan_pemusnahan/edit`, `pengadaan_barang/edit`, `permintaan_barang/edit`, `peminjaman_ruangan/edit`, `roles/create`, `roles/edit`, `gudang/create`, `gudang/edit`, `vendor/create`

**Bug JS yang ditemukan & diperbaiki:**
- `peminjaman_aset/edit`: Selector `.item-row` tidak ada di HTML → AJAX cek ketersediaan tidak pernah terpicu
- `vendor/create`: Selector `.npwp-file` / `.pakta_integritas-file` tidak ada (div pakai `data-file=...` bukan `class=...`) → toggle radio Upload/Link tidak berfungsi untuk 2 dokumen

---

### 5. Data & Session

**V1:** Session login menggunakan kolom `id_user` di tabel `sessions`. Setelah upgrade Laravel framework, nama kolom berubah menjadi `user_id` → semua login mendapat **error 500**.  
**V2:** Migrasi `fix_sessions_rename_id_user_to_user_id` merename kolom (ALTER TABLE, tidak membuang data). Login kembali normal.

**V1:** Catatan penolakan permintaan kendaraan tidak tersimpan (`reject()` tidak menerima `Request`).  
**V2:** `reject()` memvalidasi dan menyimpan catatan admin. Halaman detail menampilkan "Alasan Penolakan" saat status ditolak.

**V1:** Badge status "Tersedia" di permintaan barang selalu abu-abu (string `'Sudah Tersedia'` tidak cocok dengan nilai DB `'Tersedia'`).  
**V2:** String disesuaikan → badge tampil biru (info).

---

### 6. Tampilan

**V1 → V2:**
- Layout PDF direfactor jadi 2 tipe: **Tipe A** (laporan biasa, logo atas) dan **Tipe B** (surat resmi, kop surat + footer per halaman)
- Posisi logo geser diperbaiki di beberapa ekspor PDF
- Colspan tabel kosong (`@empty`) diperbaiki di 7 halaman (pengadaan, ekspedisi, peminjaman, pemusnahan)
- Halaman detail aset: kode aset (misal `A0025`) kini jadi judul utama — mudah dibaca saat buka banyak tab

---

### 7. Fitur Baru

| Fitur | Deskripsi |
|---|---|
| **Menu Manual Pengguna** | Link ke PDF panduan di sidebar bawah. Perlu file `Manual-SIMASTER.pdf` diunggah ke server (lihat Bagian B Langkah 4) |
| **Tombol Pindahkan Semua (bulk)** | Di detail pemindahan aset: pindahkan semua aset yang belum dipindah sekaligus, dengan konfirmasi eksplisit dan guard anti-duplikat AsetLog |
| **Export PDF Lengkap** | Di laporan tahunan: tombol baru menghasilkan PDF semua section dalam satu file. Tombol lama berlabel "Export PDF (Tab Aktif)" |
| **Seed menu gudang** | `AddGudangMenusSeeder` mendaftarkan menu Transaksi Gudang & Stok Opname secara aditif |

---

## BAGIAN B — Cara Upgrade dari V1

> **Ini bukan fresh install. JANGAN jalankan `migrate:fresh`, `db:seed` (DatabaseSeeder), atau `db:wipe` — akan menghapus semua data produksi.**

### Langkah Upgrade (ikuti berurutan)

```
[ ] 1. git pull
[ ] 2. composer install --no-dev --optimize-autoloader
[ ] 3. npm ci && npm run build
[ ] 4. php artisan migrate                               ← WAJIB KRITIS
[ ] 5. php artisan db:seed --class=AddGudangMenusSeeder  ← WAJIB
[ ] 6. Upload Manual-SIMASTER.pdf ke public/manual/      ← WAJIB (manual)
[ ] 7. php artisan config:clear && php artisan view:clear && php artisan route:clear && php artisan cache:clear
[ ] 8. Restart queue worker (supervisor / docker)
```

---

#### Langkah 1 — `git pull` (aman, fast-forward)

`git pull` dari V1 ke V2 berjalan **mulus sebagai fast-forward**.

Titik sambung antara V1 dan V2 adalah commit `af5dab2` — hash-nya **tidak berubah** selama proses update V2. Git cukup maju 21 commit ke depan tanpa konflik.

```bash
git pull
```

> **⚠️ Kasus tepi — jika `git pull` ditolak dengan pesan "rejected" atau "divergent branches":**  
> Ini terjadi jika kamu sempat melakukan `git pull` di antara push pertama V2 dan force-push V2 final (hash commit berubah). Dalam kasus ini:
> ```bash
> # Simpan perubahan lokal dulu jika ada
> git stash
>
> # Reset ke versi remote
> git fetch origin
> git reset --hard origin/main
>
> # Kembalikan perubahan lokal jika ada
> git stash pop
> ```
> ⚠️ **`git reset --hard` akan menghapus semua perubahan lokal yang belum di-commit secara permanen.** Pastikan sudah `git stash` atau backup perubahan lokal sebelum menjalankan ini.

---

#### Langkah 4 — Migrasi Database (WAJIB KRITIS)

```bash
php artisan migrate
```

**Migrasi:** `2026_09_01_152443_fix_sessions_rename_id_user_to_user_id`  
**Efek:** Rename kolom `id_user` → `user_id` di tabel `sessions`.  
**Tanpa ini:** Semua user mendapat **error 500 saat login**. Aman dijalankan pada data aktif (ALTER TABLE biasa, tidak membuang data).

```bash
# Verifikasi status sebelum migrate:
php artisan migrate:status
# Migrasi di atas harus berstatus "Pending"
```

---

#### Langkah 5 — Seeder Aditif Menu Gudang (WAJIB)

```bash
php artisan db:seed --class=AddGudangMenusSeeder
```

**Efek:** Menambah 2 menu ke tabel `menus`: **Transaksi Gudang** dan **Stok Opname**.  
**Tanpa ini:** Dua halaman gudang tidak muncul di sidebar dan tidak bisa diakses via RBAC.  
**Aman diulang:** Menggunakan `firstOrCreate` — tidak membuat duplikat jika dijalankan dua kali.

Setelah seeder jalan, buka **Admin → Roles** dan tambahkan kedua menu ke role yang perlu akses gudang.

---

#### Langkah 6 — Upload File PDF Manual Pengguna (WAJIB, manual)

File PDF tidak disertakan di git (21 MB). Sidebar menampilkan link "Manual Pengguna" yang mengarah ke file ini — jika file tidak ada, link mengembalikan 404.

```bash
# Via SCP dari mesin lokal:
scp "Manual-SIMASTER.pdf" user@server:/opt/manajemen-aset/public/manual/
```

File harus bisa diakses di: `https://domain-kamu/manual/Manual-SIMASTER.pdf`

> Minta file `Manual-SIMASTER.pdf` dari pemilik project jika belum punya.

---

#### Jika Pakai Docker Compose

```bash
cd /opt/manajemen-aset
git pull
docker compose -f docker-compose.prod.yml up -d --build

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

## Pemeriksaan Keamanan

| Pemeriksaan | Status | Catatan |
|---|---|---|
| `.env` tidak ter-commit | ✅ Aman | Ada di `.gitignore` akar |
| `database/database.sqlite` tidak ter-commit | ✅ Aman | Ada di `database/.gitignore` (`*.sqlite*`) |
| File upload tidak ter-commit | ✅ Aman | `storage/app/public/*/` ada di `.gitignore` |
| `Manual-SIMASTER.pdf` tidak ter-commit | ✅ Aman | Untracked, upload manual ke server |
| Tidak ada file debug/test tidak sengaja ter-commit | ✅ Aman | Seluruh 21 commit telah diperiksa |
| `.env.example` ter-commit | ✅ Sengaja | Template konfigurasi, tidak berisi nilai sensitif |

---

## Daftar File yang Berubah (Ringkasan)

| Kategori | File Utama |
|---|---|
| Controller | `PengaduanKerusakanController`, `PeminjamanRuanganController`, `PemindahanAsetController`, `PermintaanKendaraanController`, `GudangController`, + 5 controller form publik |
| View — form | 13 form internal edit + 6 form publik + `ruangan/dashboard.blade.php` |
| View — show/laporan | `peminjaman_aset/show`, `pemindahan_aset/show`, `laporan_tahunan/show` |
| Database | 1 migrasi (`fix_sessions`), 1 seeder (`AddGudangMenusSeeder`) |
| Layout/Partial | `sidebar.blade.php`, `pdf/layout.blade.php`, `pdf/layout-surat.blade.php` (baru) |
| Routes | `web.php` — 3 endpoint dipindah ke publik, 5 dead route dihapus |
| Komponen Blade | `resources/views/components/form-errors.blade.php` (baru) |
