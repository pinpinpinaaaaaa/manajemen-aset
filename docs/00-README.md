# Dokumentasi Teknis — Sistem Informasi Manajemen Aset

> **Versi dokumen:** September 2026  
> **Institusi:** **[PERLU KONFIRMASI]** — nama institusi belum dikonfirmasi dari kode.  
> **Repository:** `pinpinpinaaaaaa/manajemen-aset`

---

## Tujuan Sistem

Sistem Informasi Manajemen Aset adalah aplikasi web internal berbasis **Laravel 12** yang mengelola seluruh siklus hidup aset fisik dan kegiatan operasional institusi, meliputi:

- **Inventarisasi aset** — pencatatan aset inventaris, APAR, dan kendaraan beserta kondisi dan lokasinya
- **Manajemen infrastruktur** — data gedung, ruangan, gambar, dan denah
- **Transaksi operasional** — peminjaman, pemindahan, pengadaan, permintaan barang gudang, ekspedisi, pengaduan kerusakan, dan maintenance
- **Manajemen gudang** — stok barang, transaksi masuk/keluar, stok opname, dan rekap bulanan otomatis
- **Anggaran (RKAT)** — perencanaan anggaran tahunan per pos kegiatan, pencatatan realisasi, dan laporan serapan
- **Laporan & audit** — laporan tahunan, laporan pemusnahan, audit log seluruh perubahan data

---

## Pengguna Sistem

| Tipe Pengguna | Cara Akses | Keterangan |
|---|---|---|
| **Staff / Admin internal** | Login dengan akun — `/login` | Akses dikontrol per role + menu |
| **Superadmin** | Login dengan akun — akses penuh | Approve transaksi gudang, manage user/role/menu |
| **Pengguna eksternal** | Form publik — tanpa login | Pengajuan peminjaman, ekspedisi, pengaduan, permintaan barang/kendaraan |

Kontrol akses berbasis **RBAC**: setiap role memiliki daftar menu yang diizinkan. Akses bisa di-override per user individual.

---

## Daftar Isi Dokumentasi

| File | Isi |
|---|---|
| [01-tech-stack.md](01-tech-stack.md) | Teknologi, library, versi, kebutuhan server |
| [02-arsitektur.md](02-arsitektur.md) | Diagram arsitektur, struktur folder, pola desain, RBAC |
| [03-alur-sistem.md](03-alur-sistem.md) | Alur tiap modul transaksional + sequence diagram |
| [04-alur-data.md](04-alur-data.md) | ERD per modul, data flow, daftar endpoint |
| [05-setup-deployment.md](05-setup-deployment.md) | Setup lokal, Docker, env vars, backup & restore |
| [06-troubleshooting.md](06-troubleshooting.md) | Log, masalah umum, diagnosa, Cloudflare tunnel |
| [07-pertanyaan-terbuka.md](07-pertanyaan-terbuka.md) | Semua [PERLU KONFIRMASI] + risk register |
| [08-sop-operasional.md](08-sop-operasional.md) | SOP harian: user, role, menu, cache, queue, rekap |
