# Known Issues — Ditemukan Saat Verifikasi Manual Book

Daftar ini mencatat temuan kosmetik/minor yang belum diperbaiki.
Temuan kritis yang sudah diperbaiki ada di git log.

---

## KI-001 — Badge status "Tersedia" tampil abu-abu di Permintaan Barang Gudang

**File:** `resources/views/permintaan_barang/index.blade.php`
**Baris:** ~105-112 (match statement di kolom Status)

**Masalah:**
Badge match pakai string `'Sudah Tersedia'` tapi `markAvailable()` di controller
menyimpan status sebagai `'Tersedia'`. Akibatnya badge tampil abu-abu (default/secondary)
alih-alih biru (info) saat status 'Tersedia'.

**Dampak:** Kosmetik saja — tombol-tombol tetap berfungsi benar karena kondisi
tombol `@if ($p->status == 'Tersedia')` sudah cocok.

**Fix:** Ganti `'Sudah Tersedia' => 'info'` menjadi `'Tersedia' => 'info'` di match statement.

---

## KI-002 — Tombol "Pindahkan Semua" tidak ada di Pemindahan Aset

**File:** `resources/views/pemindahan_aset/show.blade.php`
**Route ada di:** `routes/web.php` baris 110 → `POST /pemindahan_aset/{id}/pindahkan`
**Controller:** `PemindahanAsetController::pindahkan()` (baris 429)

**Masalah:**
Method `pindahkan()` (bulk) memindahkan semua item sekaligus dalam satu klik.
Route-nya sudah terdaftar dan method-nya fungsional (loop melalui semua detail,
update lokasi aset, update status, catat AsetLog). Namun tidak ada tombol/link
di view mana pun yang memanggil route ini — seluruh views hanya menggunakan
`pindahkan_detail` (per-item).

**Dampak:** Fitur bulk tidak dapat diakses user. Admin harus klik "Pindahkan"
satu per satu untuk setiap aset, meski method untuk melakukan semuanya sekaligus
sudah ada.

**Penilaian:** Ini "fitur bulk yang belum dibangun tombolnya" — pola sama
persis dengan bug Serahkan/Kembalikan yang sudah diperbaiki sebelumnya. Bukan
dead code sisa desain lama — route-nya secara eksplisit didefinisikan dan
method-nya 100% fungsional.

**Saran fix:** Tambah tombol "Pindahkan Semua" di show.blade.php, tampilkan
hanya jika `decision_status == 'disetujui'` dan ada item yang belum dipindahkan.
Tombol mengarah ke `route('pemindahan_aset.pindahkan', $pemindahan->id_pemindahan)`.
Keputusan implementasi ada di pemilik proyek.

---

## KI-003 — Export Laporan Tahunan "Full" (semua seksi) tidak terjangkau dari UI

**File:** `resources/views/laporan_tahunan/show.blade.php`
**Controller:** `LaporanTahunanController::exportPdf()` (baris 226)

**Masalah:**
Controller `exportPdf()` mendukung dua mode:
- **Per-seksi:** `?section=pengadaan/gudang/maintenance/pemusnahan` → export satu tab saja
- **Full:** tanpa parameter `?section` → load `laporan_tahunan.pdf.full` (semua seksi)

Namun tombol "Export PDF" di show.blade.php **selalu** mengirimkan `?section=` berisi
tab yang sedang aktif (via JS `url += '?section=' + activeSection`). Variabel `activeSection`
selalu terisi sejak halaman pertama dimuat (default `'pengadaan'` lewat tab yang di-click otomatis).
Tidak ada tombol, link, atau cara lain di UI untuk memanggil export tanpa parameter `?section`.

**Dampak:** User hanya bisa export PDF satu seksi sekaligus.
Untuk laporan lengkap harus export 4 kali (tiap tab satu kali).
View `laporan_tahunan.pdf.full` sudah siap di server tapi tidak dapat dijangkau dari UI.

**Saran fix:** Tambah tombol "Export PDF Lengkap" yang memanggil URL export tanpa `?section`,
atau buat pilihan dropdown "Export per seksi / Export semua seksi" di halaman detail.
Keputusan implementasi ada di pemilik proyek.

---

## KI-004 — Catatan Penolakan Kendaraan tidak tersimpan ke database

**File:** `resources/views/permintaan_kendaraan/index.blade.php` (modal rejectModal)
**Controller:** `PermintaanKendaraanController::reject()` (baris 310)

**Masalah:**
Modal "Tolak Permintaan Kendaraan" menampilkan textarea `name="catatan"` yang `required`
di HTML, meminta admin mengisi alasan penolakan. Namun method `reject()` di controller
hanya melakukan `$data->update(['status' => 'ditolak'])` — field `catatan` dari request
tidak pernah dibaca atau disimpan.

Akibatnya: form memvalidasi catatan di sisi klien (required), tapi server membuang nilainya.
Catatan penolakan tidak tersimpan ke database.

**Dampak:** Pengaju tidak bisa melihat alasan penolakan. Kolom catatan di tabel
`permintaan_kendaraan` tetap null/kosong meski admin sudah mengisi.

**Fix:** Tambahkan `$request->validate(['catatan' => 'nullable|string'])` dan
`'catatan' => $request->catatan` ke dalam `update([...])` di method `reject()`.

---

## KI-006 — Menu "Transaksi Gudang" dan "Stok Opname" tidak ada entry di tabel menus

**File:** `database/seeders/MenuSeeder.php`
**Tabel:** `menus`

**Masalah:**
Sidebar menggunakan `canMenu('gudang.transaksi.index')` dan `canMenu('gudang.stok_opname.index')`
untuk menampilkan dua menu di Manajemen Gudang. Namun **tidak ada baris di tabel `menus`**
dengan kedua `route_name` tersebut — MenuSeeder hanya menyeed `gudang.index`, `gudang.dashboard`,
`gudang.laporan_transaksi`, dan `gudang.laporan_opname`, tapi tidak `gudang.transaksi.index`
maupun `gudang.stok_opname.index`.

Akibatnya:
- `canMenu()` selalu return `false` untuk kedua menu ini pada user restricted (tidak ada route_name
  di DB yang bisa dicocokkan).
- Admin RBAC tidak bisa assign akses Transaksi Gudang dan Stok Opname ke role manapun — menu tidak
  akan pernah muncul meski role-nya sudah "diberi akses" lewat UI Hak Akses.
- Superadmin tidak terpengaruh (canMenu return `true` langsung karena `allowedMenuIds = null`).

**Route_name yang harus di-seed:**

| Menu | route_name | Route asli di web.php |
|------|-----------|----------------------|
| Transaksi Barang Gudang | `gudang.transaksi.index` | `Route::get('gudang/transaksi/index', ...)` baris 259 |
| Rekap Stok Gudang | `gudang.stok_opname.index` | `Route::get('gudang/stok_opname', ...)` baris 266 |

**Fix:** Tambahkan dua `Menu::create()` di `MenuSeeder.php` di dalam folder Gudang (parent = id folder gudang),
lalu jalankan `php artisan db:seed --class=MenuSeeder` (hati-hati: seeder ini `truncate()` tabel dulu —
perlu backup data menu atau ubah seeder jadi pakai `firstOrCreate`). Setelah di-seed, admin bisa assign
kedua menu ke role yang sesuai.

---
