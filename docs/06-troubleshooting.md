# 06 — Troubleshooting

## Lokasi Log

| Log | Lokasi | Isi |
|---|---|---|
| **Laravel app log** | `storage/logs/laravel.log` | Error PHP, exception, query error, custom `Log::error()` |
| **Nginx access log** | stdout container `nginx` | Semua HTTP request + status code |
| **Nginx error log** | stderr container `nginx` | Nginx error (502, 504, config error) |
| **MySQL log** | container `mysql` stdout | Query errors, connection errors |
| **Queue worker log** | stdout container `worker` | Job processed/failed |
| **Cloudflare tunnel log** | stdout container `app` | Tunnel connect/disconnect |

### Cara Akses Log di Docker

```bash
# Log Laravel real-time
docker compose logs -f app
# Atau langsung baca file (lebih informatif):
docker compose exec app tail -f storage/logs/laravel.log

# Log Nginx
docker compose logs -f nginx

# Log queue worker
docker compose logs -f worker

# Log scheduler
docker compose logs -f scheduler
```

### Log Tailing di Dev (non-Docker)

```bash
composer run dev
# Sudah include: php artisan pail (log tailing real-time)
```

---

## Masalah Umum & Solusi

### 1. HTTP 500 — Internal Server Error

**Gejala**: Halaman menampilkan error 500 (atau halaman putih kosong di production).

**Langkah diagnosa**:
```bash
# Cek log terbaru
docker compose exec app tail -20 storage/logs/laravel.log

# Cek apakah .env terkonfigurasi benar
docker compose exec app php artisan env
```

**Penyebab umum**:
- `APP_KEY` kosong → jalankan `php artisan key:generate`
- `.env` belum di-copy → `cp .env.example .env`
- Permission error di `storage/` → `chmod -R 775 storage bootstrap/cache`
- Database belum migrate → `php artisan migrate`

---

### 2. HTTP 502 Bad Gateway

**Gejala**: Nginx mengembalikan 502 ketika PHP-FPM tidak bisa diakses.

**Penyebab & solusi**:
```bash
# Cek apakah container app berjalan
docker compose ps

# Restart container app
docker compose restart app

# Cek log app
docker compose logs app --tail=50
```

Penyebab umum: container `app` crash saat startup (misal: database belum siap, migration gagal).

---

### 3. Database Connection Failed

**Gejala**: `SQLSTATE[HY000] [2002] Connection refused` atau `Access denied for user`

**Dev (SQLite)**:
```bash
# Pastikan file sqlite ada
ls -la database/database.sqlite
# Jika tidak ada:
touch database/database.sqlite
php artisan migrate
```

**Dev (MySQL Docker)**:
```bash
# Cek container mysql berjalan
docker compose ps mysql

# Cek kredensial di .env cocok dengan docker-compose.yml
grep DB_ .env
grep MYSQL_ docker-compose.yml
```

**Production (via Cloudflare Tunnel)**:
```bash
# Cek cloudflared berjalan di container
docker compose exec app ps aux | grep cloudflared

# Cek variabel CF_ACCESS_* tersedia
docker compose exec app printenv | grep CF_ACCESS

# Cek log cloudflared
docker compose logs app | grep cloudflared
```

Jika cloudflared gagal koneksi: verifikasi credential di Cloudflare Zero Trust dashboard. Service token mungkin kadaluarsa atau dicabut.

---

### 4. Queue Worker Mati / Job Tidak Diproses

**Gejala**: Email tidak terkirim, atau job yang seharusnya diproses asinkron tidak berjalan.

**Diagnosa**:
```bash
# Cek status container worker
docker compose ps worker

# Cek log worker
docker compose logs worker --tail=50

# Cek tabel jobs di database
docker compose exec app php artisan tinker
>>> DB::table('jobs')->count()    # job pending
>>> DB::table('failed_jobs')->count()  # job gagal
```

**Solusi**:
```bash
# Restart worker
docker compose restart worker

# Retry semua failed jobs
docker compose exec app php artisan queue:retry all

# Jika ada failed job spesifik
docker compose exec app php artisan queue:failed
docker compose exec app php artisan queue:retry <id>

# Flush semua failed jobs (hati-hati — data job hilang)
docker compose exec app php artisan queue:flush
```

---

### 5. Scheduler Tidak Berjalan / Rekap Bulanan Tidak Terjadwal

**Gejala**: `rekap:bulanan` tidak berjalan otomatis tiap tanggal 1.

**Diagnosa**:
```bash
# Cek container scheduler berjalan
docker compose ps scheduler

# Cek log scheduler
docker compose logs scheduler --tail=50

# Verifikasi jadwal terdaftar
docker compose exec app php artisan schedule:list
```

**Solusi**:
```bash
# Restart scheduler
docker compose restart scheduler

# Jalankan rekap manual (jika terlambat)
docker compose exec app php artisan rekap:bulanan

# Test schedule tanpa menunggu waktu
docker compose exec app php artisan schedule:run
```

---

### 6. File Upload Tidak Muncul / Storage Link Broken

**Gejala**: Gambar aset/gedung muncul sebagai broken image, atau file tidak bisa didownload.

**Diagnosa**:
```bash
# Cek symlink storage ada
ls -la public/storage
# Harus menunjuk ke: ../storage/app/public

# Cek file ada di volume
docker compose exec app ls storage/app/public/
```

**Solusi**:
```bash
# Buat ulang symlink
docker compose exec app php artisan storage:link

# Jika symlink sudah ada tapi broken, hapus dulu:
rm public/storage
docker compose exec app php artisan storage:link
```

Jika di production pakai named volume: pastikan volume `storage_data` ter-mount dengan benar di `docker-compose.prod.yml`.

---

### 7. Assets Tidak Ter-update / CSS Lama

**Gejala**: Perubahan CSS/JS tidak terefleksikan di browser.

**Solusi**:
```bash
# Build ulang assets
npm run build

# Clear cache Laravel
docker compose exec app php artisan view:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

# Hard refresh browser: Ctrl+Shift+R / Cmd+Shift+R
```

Vite memakai hash di nama file — ini berarti browser otomatis load versi baru setelah `npm run build`. Tapi jika cache Laravel menyimpan nama file lama, jalankan `view:clear`.

---

### 8. Halaman Tampil Tapi Data Tidak Berubah / Cache Stale

**Gejala**: Update data sudah disimpan tapi tampilan tidak berubah.

```bash
# Clear semua cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

---

### 9. Akses 403 Forbidden (Tanpa Error)

**Gejala**: User sudah login tapi mendapat 403 saat mengakses halaman tertentu.

**Penyebab**: Middleware `CheckMenuAccess` menolak akses karena route tidak ada di daftar menu yang diizinkan untuk role user tersebut.

**Diagnosa**:
```bash
# Cek di database: apakah menu dengan route_name tsb ada?
docker compose exec app php artisan tinker
>>> App\Models\Menu::where('route_name', 'nama.route.yang.bermasalah')->first()

# Cek apakah menu.id ada di role user
>>> App\Models\User::find($userId)->role->menu
```

**Solusi**: Buka halaman admin `/menus` → tambahkan menu entry untuk route tersebut. Buka `/roles` → assign menu ke role yang sesuai.

---

### 10. Error "419 Page Expired" (CSRF Token Mismatch)

**Gejala**: Form submit gagal dengan "419 Page Expired".

**Penyebab**: Session kadaluarsa atau CSRF token tidak cocok.

**Solusi**:
- User: refresh halaman dan submit ulang
- Admin: cek `SESSION_DRIVER=database` dan tabel `sessions` ada (sudah migrate)
- Jika session sering expired: cek `SESSION_LIFETIME` di `.env` (default 120 menit)

---

### 11. Migration Gagal saat Deploy

**Gejala**: `Illuminate\Database\QueryException: SQLSTATE[42S01]: Base table already exists`

**Penyebab**: Migration mencoba membuat tabel yang sudah ada (migration sudah di-apply sebelumnya tapi tabel `migrations` tidak mencatat).

**Diagnosa**:
```bash
docker compose exec app php artisan migrate:status
```

**Solusi** (jangan `migrate:fresh`!):
```bash
# Jika migration sudah applied tapi tidak tercatat:
docker compose exec app php artisan tinker
>>> DB::table('migrations')->insert(['migration' => '2026_01_01_000000_create_tabel_tsb', 'batch' => 1])

# Lalu coba migrate lagi:
docker compose exec app php artisan migrate
```

---

### 12. Cloudflare Tunnel Error — Container Tidak Bisa Koneksi DB

**Gejala**: `cloudflared: failed to connect` atau timeout koneksi database di production.

**Langkah diagnosa**:
```bash
# Cek log cloudflared
docker compose -f docker-compose.prod.yml logs app | grep -i cloudflared

# Cek apakah variabel CF terset
docker compose -f docker-compose.prod.yml exec app printenv CF_ACCESS_CLIENT_ID
```

**Penyebab umum**:
1. **Service token kadaluarsa** — buat token baru di Cloudflare dashboard
2. **Hostname salah** — verifikasi `CF_ACCESS_HOSTNAME` di `.env`
3. **Cloudflared binary tidak ada di image** — rebuild image
4. **Cloudflare service down** — cek status.cloudflarestatus.com

**Sementara jika tunnel tidak bisa dipakai** (darurat):
- Setup SSH tunnel manual ke server DB
- Ubah `DB_HOST` ke IP/host yang bisa diakses langsung (jika ada firewall rule yang mengizinkan)

---

## Checklist Diagnosa Cepat

```
□ Apakah semua container berjalan? → docker compose ps
□ Ada error di log Laravel? → tail storage/logs/laravel.log
□ Ada error di log Nginx? → docker compose logs nginx
□ Database bisa diakses? → docker compose exec app php artisan tinker >>> DB::select('select 1')
□ Migration sudah up to date? → php artisan migrate:status
□ Storage symlink ada? → ls -la public/storage
□ Cache Laravel stale? → php artisan cache:clear + config:cache
□ Queue worker berjalan? → docker compose ps worker
□ Ada failed jobs? → php artisan queue:failed
```
