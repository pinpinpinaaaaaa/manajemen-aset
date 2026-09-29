# 07 — Pertanyaan Terbuka & Risk Register

Dokumen ini mengumpulkan semua hal yang **belum dikonfirmasi** (`[PERLU KONFIRMASI]`) dan **risiko teknis** yang perlu diketahui tim operasional.

---

## Bagian A — [PERLU KONFIRMASI]

### A1. Nama Institusi

**Status**: Belum dikonfirmasi  
**Lokasi**: `docs/00-README.md`, halaman login, email (jika aktif)  
**Pertanyaan**: Apa nama resmi institusi yang menggunakan sistem ini?

---

### A2. Spesifikasi Server Production

**Status**: Belum dikonfirmasi  
**Estimasi di `01-tech-stack.md`**: 2 core CPU, 2 GB RAM, 20 GB disk  
**Pertanyaan**: Berapa CPU, RAM, disk server production aktual? OS dan provider apa?

---

### A3. Email — Aktif atau Tidak?

**Status**: Default `MAIL_MAILER=log` (tidak kirim email)  
**Pertanyaan**: Apakah sistem perlu mengirim email notifikasi (approval, penolakan)? Jika ya, konfigurasi SMTP apa yang dipakai?

---

### A4. Backup Otomatis

**Status**: Tidak ada konfigurasi backup otomatis  
**Pertanyaan**:  
- Apakah ada backup terjadwal di server (cron di luar Docker)?  
- Seberapa sering backup diharapkan?  
- Ke mana backup disimpan (lokal / cloud / NAS)?

---

### A5. Kapasitas Upload Maksimum

**Status**: Nginx dikonfigurasi `client_max_body_size 64m`; Laravel belum ada batas eksplisit selain validasi `max:2048` (2MB) per file di beberapa form  
**Pertanyaan**: Apakah ada kebijakan ukuran file upload maksimum per modul?

---

### A6. CAPTCHA di Form Publik

**Status**: Tidak ada CAPTCHA — hanya rate limiting `throttle:20,1`  
**Pertanyaan**: Apakah perlu CAPTCHA (reCAPTCHA / hCaptcha) di form publik? Lihat risk assessment di Bagian B.

---

### A7. Retensi Data Audit Log

**Status**: Tidak ada cleanup otomatis — audit_logs tumbuh tanpa batas  
**Pertanyaan**: Seberapa lama riwayat audit log perlu disimpan? Apakah boleh dihapus setelah N bulan?

---

### A8. Akses Multi-Lokasi / Multi-Gedung

**Status**: Data gedung sudah ada di sistem, tapi tidak ada pembatasan akses per-gedung  
**Pertanyaan**: Apakah user tertentu hanya boleh mengakses aset/ruangan di gedung tertentu?

---

## Bagian B — Risk Register

### R1. Tidak Ada CAPTCHA di Form Publik

| | |
|---|---|
| **Risiko** | Bot atau penyerang bisa memenuhi sistem dengan data permintaan palsu |
| **Kemungkinan** | Sedang — form publik bisa di-discover, rate limiting bisa di-bypass dengan banyak IP |
| **Dampak** | Tinggi — antrian approval penuh dengan data sampah; staff harus memilah manual |
| **Mitigasi saat ini** | `throttle:20,1` per IP — 20 request/menit |
| **Rekomendasi** | Tambahkan Google reCAPTCHA v3 (invisible) atau hCaptcha di semua form publik POST |
| **Biaya implementasi** | ~1 hari kerja — library `anhskee/laravel-recaptcha` atau integrasi manual |

---

### R2. Tidak Ada Backup Otomatis

| | |
|---|---|
| **Risiko** | Kehilangan semua data jika server/disk gagal, atau salah jalankan `migrate:fresh` |
| **Kemungkinan** | Rendah per kejadian, tapi konsekuensi permanen |
| **Dampak** | Kritis — tidak ada recovery jika database hilang |
| **Mitigasi saat ini** | Tidak ada |
| **Rekomendasi** | Jadwalkan `mysqldump` harian via cron di server database; simpan ke minimal 2 lokasi berbeda |
| **Biaya implementasi** | ~2 jam — setup cron + target backup (disk eksternal / S3 / NAS) |

---

### R3. Race Condition pada ID Generator

| | |
|---|---|
| **Risiko** | Dua request yang masuk bersamaan bisa mendapat ID yang sama (duplikat PK) |
| **Detail teknis** | ID format `PREFIX-YYYYMMDD-NNNN` di-generate dengan: `SELECT MAX(id) LIKE "PJM-YYYYMMDD-%" ORDER BY id DESC LIMIT 1` → increment. Tanpa lock, dua request yang masuk dalam hitungan milidetik bisa membaca angka terakhir yang sama. |
| **Kemungkinan** | Rendah di kondisi normal (traffic rendah), meningkat saat banyak user submit bersamaan |
| **Dampak** | Sedang — gagal insert karena PK conflict (akan ada error 500), tapi data tidak corrupt |
| **Mitigasi saat ini** | `DB::transaction()` + `lockForUpdate()` saat `store()` di beberapa modul |
| **Catatan** | Perlu audit: tidak semua modul menggunakan `lockForUpdate()` secara konsisten |
| **Rekomendasi** | Audit semua `generateId()` di controller — pastikan semuanya wrapped dalam transaction + lockForUpdate, atau migrasi ke UUID/auto-increment |

---

### R4. Audit Log Tumbuh Tanpa Batas

| | |
|---|---|
| **Risiko** | Tabel `audit_logs` bisa tumbuh sangat besar seiring waktu, memperlambat query |
| **Kemungkinan** | Pasti — setiap CRUD di semua model menghasilkan 1+ record audit |
| **Dampak** | Sedang — query ke `audit_logs` menjadi lambat setelah jutaan record; disk penuh |
| **Rekomendasi** | Tambahkan scheduled command untuk archive/delete audit log > N bulan; atau partisi tabel berdasarkan bulan |
| **Biaya implementasi** | ~1 hari kerja |

---

### R5. Tidak Ada HTTPS Enforcement

| | |
|---|---|
| **Risiko** | Jika diakses via HTTP (bukan HTTPS), session token bisa di-intercept |
| **Status** | Bergantung setup Nginx/load balancer di depan — tidak dikonfigurasi di dalam aplikasi |
| **Pertanyaan** | Apakah ada SSL termination di depan Nginx (reverse proxy, Cloudflare)?  |
| **Rekomendasi** | Aktifkan `FORCE_HTTPS=true` di `.env` + tambahkan `Str::forceScheme('https')` di `AppServiceProvider` jika HTTPS termination ada di luar container |

---

### R6. Validasi Upload File — Tipe & Ukuran

| | |
|---|---|
| **Risiko** | Penyerang bisa upload file berbahaya (script PHP, malware) menyamar sebagai gambar |
| **Mitigasi saat ini** | `mimes:jpg,jpeg,png,pdf` + `max:2048` validasi Laravel di beberapa form; `intervention/image` resize gambar (menghilangkan metadata berbahaya) |
| **Celah** | Tidak semua form upload menggunakan validasi yang sama; file PDF tidak di-scan |
| **Rekomendasi** | Audit semua form dengan file upload, pastikan semua pakai `mimes` + `max` validation secara konsisten |

---

### R7. Queue Failure Tanpa Alert

| | |
|---|---|
| **Risiko** | Queue worker gagal/mati tanpa ada notifikasi ke admin |
| **Status** | Tidak ada alerting |
| **Dampak** | Rendah saat ini (queue dipakai untuk job non-kritis), bisa meningkat jika email notifikasi diaktifkan |
| **Rekomendasi** | Setup health check container worker + alert (email / Slack) jika container down |

---

## Ringkasan Prioritas

| Risk | Prioritas | Effort |
|---|---|---|
| R2 — Tidak ada backup | **Kritis** | Rendah (~2 jam) |
| R1 — Tidak ada CAPTCHA | Tinggi | Sedang (~1 hari) |
| R4 — Audit log tanpa batas | Sedang | Sedang (~1 hari) |
| R3 — Race condition ID | Sedang | Rendah (~2 jam audit) |
| R5 — HTTPS | Sedang | Perlu konfirmasi infrastruktur |
| R6 — Upload validation | Sedang | Rendah (~2 jam audit) |
| R7 — Queue alert | Rendah | Sedang |
