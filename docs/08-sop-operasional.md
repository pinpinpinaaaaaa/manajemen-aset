# 08 — SOP Operasional

Panduan langkah-per-langkah untuk tugas rutin administrasi sistem. Semua aksi di sini membutuhkan akses superadmin kecuali disebutkan lain.

---

## SOP-01: Tambah User Baru

**Halaman**: `/users` → tombol "Tambah User"

**Langkah**:
1. Login sebagai superadmin
2. Buka menu **Admin → Kelola Pengguna** (`/users`)
3. Klik tombol **Tambah User**
4. Isi form:
   - **Nama**: nama lengkap
   - **Email**: email yang akan dipakai untuk login
   - **Password**: sementara (minta user ganti sendiri setelah pertama login)
   - **Role**: pilih role yang sesuai
5. Klik **Simpan**
6. Opsional: jika user perlu akses lebih terbatas dari rolenya, set **menu override** individual (lihat SOP-04)

**Catatan**: Email harus unik di sistem. Jika email sudah terdaftar akan muncul validasi error.

---

## SOP-02: Nonaktifkan / Hapus User

> **Perhatian**: Hapus user akan menghapus record dari tabel `users`. Audit log yang terkait tetap ada (relasi ke `id_user` bisa menjadi null atau orphan tergantung konfigurasi FK).

**Opsi 1 — Nonaktifkan (direkomendasikan)**:
Saat ini belum ada field `is_active` di tabel users. Cara sementara: **ganti password** user tersebut ke string random agar tidak bisa login.

```bash
# Via Artisan Tinker
docker compose exec app php artisan tinker
>>> App\Models\User::find($id)->update(['password' => bcrypt(Str::random(32))])
```

**Opsi 2 — Hapus permanen**:
1. Buka `/users`
2. Klik ikon hapus di baris user
3. Konfirmasi dialog

> **[PERLU KONFIRMASI]** Pertimbangkan menambahkan field `is_active` ke tabel users untuk non-aktifkan tanpa hapus.

---

## SOP-03: Reset Password User

**Via halaman admin**:
1. Buka `/users`
2. Klik **Edit** di baris user
3. Isi field **Password Baru** + **Konfirmasi Password**
4. Klik **Update**

**Via Artisan Tinker** (jika admin tidak bisa akses UI):
```bash
docker compose exec app php artisan tinker
>>> App\Models\User::where('email', 'user@example.com')->update(['password' => bcrypt('password_baru')])
```

---

## SOP-04: Buat Role Baru

**Halaman**: `/roles` → tombol "Tambah Role"

**Langkah**:
1. Buka menu **Admin → Kelola Role** (`/roles`)
2. Klik **Tambah Role**
3. Isi **Nama Role**
4. Pilih **menu** yang diizinkan untuk role ini (checkbox list)
5. Klik **Simpan**
6. Assign role ke user: buka `/users` → Edit user → pilih role baru

---

## SOP-05: Set Akses Menu per Role (RBAC)

**Halaman**: `/roles`

**Konsep**:
- Setiap role memiliki daftar menu ID yang diizinkan (disimpan sebagai JSON di kolom `menu`)
- User bisa punya **override individual** (kolom `menu` di tabel `users`) yang menimpa role

**Ubah akses role**:
1. Buka `/roles`
2. Klik **Edit** di role yang ingin diubah
3. Centang/hapus centang menu yang diizinkan
4. Klik **Simpan**

**Override per user individual**:
1. Buka `/users`
2. Klik **Edit** di user
3. Di bagian **Menu Override**: centang menu spesifik
4. Jika kosong (tidak ada yang dicentang) → user mengikuti menu dari rolenya
5. Klik **Update**

**Catatan**: Jika `menu` di user DAN role keduanya null → user mendapat **full access** (berlaku untuk superadmin).

---

## SOP-06: Tambah Menu Baru ke Sistem

**Dibutuhkan saat**: route baru ditambahkan dan perlu dikontrol aksesnya via RBAC.

**Halaman**: `/menus`

**Langkah**:
1. Buka **Admin → Kelola Menu** (`/menus`)
2. Klik **Tambah Menu**
3. Isi:
   - **Nama**: label yang tampil di sidebar
   - **Route Name**: nama route Laravel (dari `routes/web.php`) — harus sama persis
   - **Icon**: class ikon (contoh: `fas fa-box`)
   - **Parent**: jika sub-menu, pilih menu parent
   - **Urutan**: angka urutan tampil di sidebar
4. Klik **Simpan**
5. Assign menu baru ke role yang seharusnya bisa akses (SOP-05)

**Catatan teknis**: Jika route tidak ada record di tabel `menus`, middleware `CheckMenuAccess` akan **mengizinkan akses** untuk semua user yang sudah login (bukan 403). Ini desain by default — hanya route yang terdaftar di `menus` yang dikontrol aksesnya.

---

## SOP-07: Clear Cache (Setelah Deploy / Konfigurasi Berubah)

Jalankan setelah: deploy baru, ubah `.env`, ubah konfigurasi di `config/`.

```bash
# Via Docker
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan cache:clear

# Lalu rebuild cache untuk performance
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

**Perhatian**: `config:cache` akan membuat semua `env()` call di luar `config/` tidak berfungsi. Pastikan semua env variable diakses lewat `config()`, bukan `env()` langsung di controller/view.

---

## SOP-08: Restart Queue Worker

**Kapan dibutuhkan**: Setelah deploy kode baru (agar worker pakai kode terbaru), atau jika worker mati.

```bash
# Restart container worker
docker compose restart worker

# Atau sinyal graceful restart (worker selesaikan job berjalan dulu)
docker compose exec app php artisan queue:restart

# Lihat status worker
docker compose logs worker --tail=20
```

**Jika ada failed jobs yang perlu di-retry**:
```bash
# Lihat daftar failed jobs
docker compose exec app php artisan queue:failed

# Retry semua
docker compose exec app php artisan queue:retry all

# Retry job tertentu (gunakan ID dari queue:failed)
docker compose exec app php artisan queue:retry 5
```

---

## SOP-09: Jalankan Rekap Bulanan Gudang Manual

Rekap bulanan gudang berjalan **otomatis** tiap tanggal 1 jam 00:00 via scheduler. Jika perlu dijalankan manual (contoh: scheduler bermasalah, atau rekap bulan lalu terlewat):

```bash
# Jalankan rekap (menggunakan bulan sebelumnya secara default)
docker compose exec app php artisan rekap:bulanan

# Lihat hasil rekap
# Buka: /gudang/rekap di browser (halaman rekap bulanan)
```

**Catatan**: Command ini melakukan snapshot `stok_akhir` ke tabel `gudang_rekap_bulanan`. Aman dijalankan berulang — akan update record yang sudah ada (upsert berdasarkan id_barang + bulan + tahun).

---

## SOP-10: Export Data ke Excel/PDF

Tersedia di berbagai halaman daftar (index). Tidak ada langkah khusus — klik tombol Export di UI.

| Modul | Format | Tombol di |
|---|---|---|
| Aset Inventaris | Excel, PDF | `/aset` |
| Peminjaman Aset | PDF | `/peminjaman-aset` |
| Gudang Stok | Excel | `/gudang` |
| Gudang Rekap | Excel | `/gudang/rekap` |
| Laporan Tahunan | Excel, PDF | `/laporan-tahunan` |

File di-generate real-time dan langsung di-download ke browser. File tidak disimpan di server.

---

## SOP-11: Backup Manual Database

Jalankan sebelum: deploy major, perubahan migrasi besar, atau sebelum maintenance.

```bash
# Backup dari container MySQL (dev)
docker compose exec mysql mysqldump -u root -p manajemen_aset > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup dari server MySQL eksternal (prod)
# Jalankan dari server database, bukan dari container:
mysqldump -u USERNAME -p DATABASE_NAME > /backups/backup_$(date +%Y%m%d_%H%M%S).sql
```

Simpan file backup di lokasi aman, **bukan di dalam container** (container data hilang saat recreate).

---

## SOP-12: Proses Darurat — Database Tiba-tiba Tidak Bisa Diakses (Production)

**Langkah**:

1. **Cek log container app**:
   ```bash
   docker compose -f docker-compose.prod.yml logs app --tail=50
   ```

2. **Cek cloudflared** (jika pakai CF tunnel):
   ```bash
   docker compose -f docker-compose.prod.yml exec app ps aux | grep cloudflared
   ```
   Jika cloudflared tidak berjalan: restart container `app`
   ```bash
   docker compose -f docker-compose.prod.yml restart app
   ```

3. **Verifikasi credential CF masih valid**:
   - Login ke Cloudflare Zero Trust dashboard
   - Cek service token tidak kadaluarsa
   - Jika kadaluarsa: buat token baru, update `.env`, restart `app`

4. **Cek server database on-premise** berjalan normal (tanya tim IT on-premise)

5. **Jika tidak bisa diselesaikan segera**: pertimbangkan mode maintenance
   ```bash
   docker compose -f docker-compose.prod.yml exec app php artisan down --message="Sistem sedang dalam perbaikan. Silakan coba lagi nanti."
   # Setelah masalah teratasi:
   docker compose -f docker-compose.prod.yml exec app php artisan up
   ```

---

## Referensi Cepat

| Perintah | Fungsi |
|---|---|
| `php artisan migrate` | Jalankan migration baru |
| `php artisan migrate:status` | Cek status migration |
| `php artisan rekap:bulanan` | Rekap stok gudang manual |
| `php artisan queue:restart` | Restart worker (graceful) |
| `php artisan queue:failed` | Lihat job gagal |
| `php artisan queue:retry all` | Retry semua job gagal |
| `php artisan cache:clear` | Hapus cache aplikasi |
| `php artisan config:cache` | Rebuild config cache |
| `php artisan route:cache` | Rebuild route cache |
| `php artisan view:clear` | Hapus compiled views |
| `php artisan down` | Mode maintenance |
| `php artisan up` | Matikan mode maintenance |
| `php artisan tinker` | REPL interaktif untuk debug |
