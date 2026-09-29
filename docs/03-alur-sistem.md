# 03 — Alur Sistem per Modul

Semua modul transaksional mengikuti pola yang sama. Bagian ini mendokumentasikan alur tiap modul beserta diagram sekuens.

---

## Pola Umum Modul Transaksional

```mermaid
stateDiagram-v2
    [*] --> menunggu_persetujuan : Pengaju submit form (store)
    menunggu_persetujuan --> disetujui : Admin approve()
    menunggu_persetujuan --> ditolak : Admin reject()
    disetujui --> Selesai : Proses akhir (serahkan / kembalikan / kirim)
    ditolak --> [*]
    Selesai --> [*]
```

Dua field status selalu ada di tabel transaksional:
- **`decision_status`**: `menunggu_persetujuan` → `disetujui` / `ditolak`
- **`status`**: `Belum Diproses` → `Sedang Diproses` → `Selesai`

---

## 1. Peminjaman Aset

**Controller**: `PeminjamanAsetController`  
**Model**: `PeminjamanAset`, `PeminjamanAsetDetail`  
**Route prefix**: `/peminjaman-aset`

```mermaid
sequenceDiagram
    participant E as Peminjam (Eksternal/Internal)
    participant C as PeminjamanAsetController
    participant DB as Database
    participant A as Admin

    E->>C: GET /form_peminjaman_aset (tanpa login)
    C-->>E: Form peminjaman

    E->>C: POST /form_peminjaman_aset
    C->>DB: Cek ketersediaan (cekKetersediaan)
    C->>DB: DB::transaction - PeminjamanAset::create + PeminjamanAsetDetail::create
    Note over C,DB: decision_status = menunggu_persetujuan
    C-->>E: Redirect + flash success

    A->>C: GET /peminjaman-aset (index, login required)
    C->>DB: Query PeminjamanAset with details
    C-->>A: List pending + riwayat

    A->>C: POST /peminjaman-aset/{id}/approve
    C->>DB: Update decision_status = disetujui
    C-->>A: Redirect + success

    A->>C: POST /peminjaman-aset/{id}/serahkan
    C->>DB: Aset.status = terpakai
    C->>DB: Detail.status_pengembalian = dipinjam
    C->>DB: AsetLog::create(event=dipinjam)
    C-->>A: Redirect

    A->>C: POST /peminjaman-aset/{id}/kembalikan
    C->>DB: Aset.status = tersedia
    C->>DB: Detail.status_pengembalian = dikembalikan
    C->>DB: AsetLog::create(event=dikembalikan)
    C->>DB: Jika semua item kembali → PeminjamanAset.status = Selesai
    C-->>A: Redirect
```

**Endpoint AJAX**: `GET /cek-ketersediaan` — hitung unit tersedia di rentang tanggal tertentu (pakai `lockForUpdate()` saat `store`).

---

## 2. Peminjaman Ruangan

**Controller**: `PeminjamanRuanganController`  
**Model**: `PeminjamanRuangan`, `PeminjamanRuanganDetail`, `PeminjamanRuanganAset`, `PeminjamanRuanganKonsumsi`

Alur sama dengan Peminjaman Aset, dengan tambahan:
- Detail bisa menyertakan daftar **aset yang ada di ruangan** (`PeminjamanRuanganAset`)
- Bisa menyertakan **konsumsi** (snack, minuman) via `PeminjamanRuanganKonsumsi`
- Endpoint AJAX: `GET /cek-ketersediaan-ruangan`

```mermaid
sequenceDiagram
    participant E as Peminjam
    participant C as Controller
    participant DB as DB

    E->>C: POST /form-peminjaman-ruangan
    C->>DB: transaction - PeminjamanRuangan + Detail + Aset + Konsumsi
    Note over DB: decision_status = menunggu_persetujuan

    C-->>E: Flash success

    Note over C: (Alur approve/reject/serahkan/kembalikan sama persis)
```

---

## 3. Permintaan Barang Gudang

**Controller**: `PermintaanBarangGudangController`  
**Model**: `PermintaanBarangGudang`  
**Alur khas**: Setelah disetujui, barang diambil dari stok gudang.

```mermaid
sequenceDiagram
    participant E as Staff
    participant C as PermintaanBarangGudangController
    participant G as GudangController (internal)
    participant DB as DB

    E->>C: POST /form-permintaan-barang (publik, tanpa login)
    C->>DB: PermintaanBarangGudang::create
    Note over DB: decision_status = menunggu_persetujuan

    A->>C: POST /permintaan-barang/{id}/approve
    C->>DB: Update decision_status = disetujui

    A->>C: POST /permintaan-barang/{id}/proses
    C->>DB: transaction - kurangi stok GudangBarang
    C->>DB: GudangTransaksi::create (tipe=keluar)
    C->>DB: PermintaanBarangGudang.status = Selesai
```

---

## 4. Pengadaan Barang/Jasa (Pembelian ke Vendor)

**Controller**: `PengadaanBarangJasaController`  
**Model**: `PengadaanBarangJasa`, `PengadaanBarangJasaDetail`, `PengadaanBarangJasaFile`

```mermaid
sequenceDiagram
    participant E as Staff/Vendor (Eksternal)
    participant C as PengadaanBarangJasaController
    participant DB as DB
    participant R as RkatRealisasi

    E->>C: POST /form-pengadaan-barang (publik)
    C->>DB: transaction - PengadaanBarangJasa + Detail + File
    Note over DB: decision_status = menunggu_persetujuan

    A->>C: POST /pengadaan-barang/{id}/approve
    C->>DB: decision_status = disetujui

    A->>C: POST /pengadaan-barang/{id}/selesai
    C->>DB: status = Selesai
    C->>R: RkatRealisasi::updateOrCreate (sumber_type=PengadaanBarangJasa, sumber_id=id)
    Note over R: Mencatat realisasi anggaran RKAT
```

File upload (struk, invoice) disimpan ke `storage/app/public/pengadaan/`.

---

## 5. Permintaan Kendaraan

**Controller**: `PermintaanKendaraanController`  
**Model**: `PermintaanKendaraan`, `PermintaanKendaraanDetail`, `PermintaanKendaraanItem`

```mermaid
sequenceDiagram
    participant E as Peminjam
    participant C as PermintaanKendaraanController
    participant DB as DB

    E->>C: POST /form-permintaan-kendaraan (publik)
    C->>DB: cek ketersediaan kendaraan (GET /cek-kendaraan)
    C->>DB: transaction - PermintaanKendaraan + Detail + Item
    Note over DB: decision_status = menunggu_persetujuan

    A->>C: approve → serahkan (Kendaraan.status = digunakan)
    A->>C: kembalikan (Kendaraan.status = tersedia)
```

---

## 6. Pemindahan Aset

**Controller**: `PemindahanAsetController`  
**Model**: `PemindahanAset`, `PemindahanAsetDetail`

```mermaid
sequenceDiagram
    participant A as Admin/Staff
    participant C as PemindahanAsetController
    participant DB as DB

    A->>C: POST /pemindahan-aset (store, harus login)
    C->>DB: transaction - PemindahanAset + Detail
    Note over DB: decision_status = menunggu_persetujuan

    A->>C: POST /pemindahan-aset/{id}/approve
    C->>DB: Aset.id_ruangan = ruangan_tujuan
    C->>DB: Aset.id_gedung = gedung_tujuan
    C->>DB: AsetLog::create(event=dipindahkan)
    C->>DB: status = Selesai
```

Pemindahan aset **tidak** mencatat realisasi RKAT (bukan pengeluaran).

---

## 7. Ekspedisi (Pengiriman Keluar)

**Controller**: `EkspedisiController`  
**Model**: `Ekspedisi`, `EkspedisiBarang`, `EkspedisiDokumen`, `EkspedisiPengiriman`

```mermaid
sequenceDiagram
    participant E as Pengirim (Eksternal)
    participant C as EkspedisiController
    participant DB as DB

    E->>C: POST /form-ekspedisi (publik)
    C->>DB: transaction - Ekspedisi + EkspedisiBarang + Dokumen
    Note over DB: status = menunggu_persetujuan

    A->>C: approve → proses pengiriman
    C->>DB: EkspedisiPengiriman::create (nomor resi, kurir)
    C->>DB: Ekspedisi.status = Terkirim

    A->>C: GET /ekspedisi/{id}/label → PDF label
    A->>C: GET /ekspedisi/{id}/tanda-terima → PDF tanda terima
```

---

## 8. Pengaduan Kerusakan

**Controller**: `PengaduanKerusakanController`  
**Model**: `PengaduanKerusakan`, `PengaduanKerusakanDetail`

Form publik tanpa login. Admin menangani laporan dan menautkannya ke Maintenance jika perlu.

```mermaid
sequenceDiagram
    participant E as Pelapor (Eksternal)
    participant C as PengaduanKerusakanController
    participant DB as DB

    E->>C: POST /form-pengaduan-kerusakan (publik)
    C->>DB: PengaduanKerusakan::create + Detail
    Note over DB: status = Belum Diproses

    A->>C: POST /pengaduan/{id}/proses
    C->>DB: status = Sedang Diproses

    A->>C: POST /pengaduan/{id}/selesai
    C->>DB: status = Selesai
```

---

## 9. Maintenance

**Controller**: `MaintenanceController`  
**Model**: `Maintenance`, `MaintenanceDetail`

```mermaid
sequenceDiagram
    participant A as Admin/Staff
    participant C as MaintenanceController
    participant DB as DB
    participant R as RkatRealisasi

    A->>C: POST /maintenance (store, harus login)
    C->>DB: Maintenance::create + MaintenanceDetail::create
    Note over DB: status = Belum Diproses

    A->>C: POST /maintenance/{id}/mulai
    C->>DB: status = Sedang Diproses

    A->>C: POST /maintenance/{id}/selesai (atau selesaiDetail)
    C->>DB: status = Selesai
    C->>DB: Aset kondisi diupdate jika ada field kondisi
    C->>R: RkatRealisasi::updateOrCreate (sumber_type=Maintenance)
    Note over R: Catat biaya maintenance ke RKAT
```

---

## 10. Stok Opname Gudang

**Controller**: `GudangController` — method `opname*()`  
**Model**: `GudangOpnameHeader`, `GudangOpnameDetail`

```mermaid
sequenceDiagram
    participant A as Admin Gudang
    participant C as GudangController
    participant DB as DB

    A->>C: GET /gudang/opname/buat
    C-->>A: Form opname (prefill stok sistem)

    A->>C: POST /gudang/opname
    C->>DB: GudangOpnameHeader::create + GudangOpnameDetail::create
    Note over DB: status = draft

    A->>C: POST /gudang/opname/{id}/submit
    C->>DB: Loop detail → GudangBarang.stok = stok_aktual
    C->>DB: GudangOpnameHeader.status = selesai
    C->>DB: GudangTransaksi::create (tipe=opname, selisih stok)
```

---

## 11. Transaksi Gudang (Masuk / Keluar)

**Controller**: `GudangController` — method `transaksiStore()`, `transaksiApprove()`  
**Model**: `GudangTransaksi`, `GudangTransaksiDetail`, `GudangTransaksiCharge`

```mermaid
sequenceDiagram
    participant A as Admin Gudang
    participant C as GudangController
    participant DB as DB
    participant R as RkatRealisasi

    A->>C: POST /gudang/transaksi (store)
    C->>DB: transaction - GudangTransaksi + Detail + Charge (biaya tambahan)
    Note over DB: status = menunggu_persetujuan

    A->>C: POST /gudang/transaksi/{id}/approve
    C->>DB: Loop detail → GudangBarang.stok += jumlah (masuk) atau -= (keluar)
    C->>DB: GudangTransaksi.status = disetujui
    C->>R: RkatRealisasi::updateOrCreate (jika ada rkat_anggaran_id)
```

---

## 12. Laporan Pemusnahan Aset

**Controller**: `LaporanPemusnahanController`  
**Model**: `LaporanPemusnahan`

```mermaid
sequenceDiagram
    participant A as Admin
    participant C as LaporanPemusnahanController
    participant DB as DB
    participant R as RkatRealisasi

    A->>C: POST /laporan-pemusnahan (store)
    C->>DB: LaporanPemusnahan::create
    C->>DB: Aset.status = dimusnahkan / dihapus
    C->>DB: AsetLog::create(event=dimusnahkan)

    Note over C,R: Dua jenis realisasi:
    C->>R: RkatRealisasi biaya keluar (biaya pemusnahan)
    C->>R: RkatRealisasi nilai masuk (PNBP jika ada hasil lelang/jual)
```

---

## 13. Rekap Bulanan Gudang (Scheduled Task)

**Command**: `php artisan rekap:bulanan`  
**Controller**: `GudangController::rekapBulan()`  
**Jadwal**: Otomatis tiap tanggal 1 jam 00:00

```mermaid
sequenceDiagram
    participant S as Scheduler (schedule:work)
    participant CMD as RekapBulanan Command
    participant C as GudangController
    participant DB as DB

    S->>CMD: Fire rekap:bulanan (tgl 1 jam 00:00)
    CMD->>C: rekapBulan()
    C->>DB: Query semua GudangBarang
    C->>DB: Loop → GudangRekapBulanan::create/update
    Note over DB: stok_akhir bulan lalu → stok_awal bulan ini
```

Bisa juga dipicu manual: `php artisan rekap:bulanan`.

---

## 14. Anggaran RKAT

**Controller**: `AnggaranController`  
**Model**: `RkatAnggaran`, `RkatRealisasi`

```mermaid
sequenceDiagram
    participant A as Admin
    participant C as AnggaranController
    participant DB as DB

    A->>C: POST /anggaran-rkat (store)
    C->>DB: RkatAnggaran::create (kode_kegiatan unik per tahun)

    Note over A,C: Realisasi dicatat OTOMATIS dari modul lain
    Note over DB: sumber_type + sumber_id = polymorphic ke GudangTransaksi / PengadaanBarangJasa / Maintenance / LaporanPemusnahan

    A->>C: GET /anggaran-rkat/{id}/show
    C->>DB: RkatRealisasi::where(rkat_anggaran_id=id)
    C-->>A: Detail per bulan (realisasi, rencana, selisih, kumulatif, sisa)
```

Realisasi **tidak dicatat manual oleh admin** — dicatat otomatis saat modul pengadaan/maintenance/gudang/pemusnahan diproses.

---

## 15. Form Publik — Keamanan

Semua form publik (tanpa login) dilindungi:

| Mekanisme | Implementasi |
|---|---|
| **Rate limiting** | `throttle:20,1` pada route POST publik — max 20 request/menit per IP |
| **CSRF** | Laravel default CSRF token di semua form |
| **Validasi input** | `$request->validate()` di controller — wajib sebelum insert DB |
| **Upload validation** | `mimes:jpg,jpeg,png,pdf` + `max:2048` (2MB) untuk file upload |
| **SQL Injection** | Eloquent ORM / parameter binding — tidak ada query raw tanpa binding |
| **XSS** | Blade `{{ }}` auto-escape; `{!! !!}` hanya untuk HTML yang sudah disanitasi |

> **[PERLU KONFIRMASI]** Tidak ada CAPTCHA di form publik — lihat [`07-pertanyaan-terbuka.md`](07-pertanyaan-terbuka.md) untuk risk assessment.
