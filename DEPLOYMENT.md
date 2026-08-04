# Panduan Deploy — Sistem Manajemen Aset

Panduan ini ditujukan untuk administrator server yang menerima project ini dan ingin menjalankannya dari nol.

---

## Prasyarat

Pastikan server sudah memiliki:

- **Docker Engine** ≥ 24 + **Docker Compose plugin** (bukan docker-compose versi lama)
- **Git**
- Akses internet (untuk pull image/package saat build pertama)
- Port **8000** terbuka di firewall (atau sesuai `APP_PORT` yang diset)

Cek apakah sudah terpasang:
```bash
docker --version
docker compose version
git --version
```

Jika belum ada Docker (contoh untuk CentOS/RHEL):
```bash
yum install -y yum-utils git
yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
yum install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
systemctl enable --now docker
```

---

## Langkah 1 — Ambil Source Code

```bash
cd /opt
git clone https://github.com/PEMILIK/manajemen-aset.git
cd manajemen-aset
```

> Jika repo private: minta akses ke pemilik repo, atau gunakan personal access token:
> `git clone https://TOKEN@github.com/PEMILIK/manajemen-aset.git`

---

## Langkah 2 — Buat File `.env`

```bash
cp .env.example .env
nano .env      # atau: vi .env
```

**Isi semua nilai berikut (yang bertanda Wajib tidak boleh kosong):**

| Variable | Keterangan |
|---|---|
| `APP_KEY` | **Wajib.** Generate dengan: `docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32));"` |
| `APP_URL` | **Wajib.** URL server, contoh: `http://192.168.1.10:8000` |
| `DB_USERNAME` | **Wajib.** Username database MySQL |
| `DB_PASSWORD` | **Wajib.** Password database MySQL |
| `CF_ACCESS_HOSTNAME` | **Wajib.** Hostname tunnel Cloudflare ke database |
| `CF_ACCESS_CLIENT_ID` | **Wajib.** Service token ID dari Cloudflare Zero Trust |
| `CF_ACCESS_CLIENT_SECRET` | **Wajib.** Service token secret dari Cloudflare Zero Trust |
| `APP_PORT` | Opsional. Port akses aplikasi (default: `8000`) |

> **Catatan DB:** `DB_HOST=127.0.0.1` sudah benar — database diakses lewat tunnel
> cloudflared yang berjalan di dalam container, bukan koneksi langsung.

Simpan file: **Ctrl+O → Enter → Ctrl+X** (jika pakai nano)

---

## Langkah 3 — Buka Port Firewall

```bash
firewall-cmd --permanent --add-port=8000/tcp
firewall-cmd --reload
```

Sesuaikan angka port jika `APP_PORT` diset berbeda.

---

## Langkah 4 — Deploy

Pilih **salah satu** cara berikut:

### Cara A — Build dari Source (lebih mudah, tidak perlu akses registry)

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

Build pertama kali membutuhkan **5–15 menit** (download base image + install dependensi).

### Cara B — Pull Image dari Registry (tidak ada source code di server)

Cara ini memerlukan akses ke registry ghcr.io milik pemilik project.

```bash
# Login ke registry (gunakan token yang diberikan pemilik project)
docker login ghcr.io -u GITHUB_USERNAME

# Tambahkan GITHUB_USER ke .env
echo "GITHUB_USER=GITHUB_USERNAME_PEMILIK" >> .env

# Pull image dan jalankan
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

---

## Langkah 5 — Pantau Startup

```bash
docker compose -f docker-compose.prod.yml logs -f app
```

Tunggu hingga muncul:
```
[entrypoint] Memulai cloudflared tunnel → ...
[entrypoint] MySQL siap.
[entrypoint] Menjalankan migrasi...
[entrypoint] App siap.
```

Cek semua container berjalan:
```bash
docker compose -f docker-compose.prod.yml ps
```

Harus ada 4 container status `running`: `app`, `nginx`, `worker`, `scheduler`.

---

## Langkah 6 — Seed Database (hanya pertama kali)

```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed
```

Perintah ini membuat akun admin awal dan data referensi. **Jangan dijalankan ulang** jika data sudah ada.

---

## Langkah 7 — Akses Aplikasi

Buka browser:
```
http://IP_SERVER:8000
```

Login menggunakan akun admin dari seeder. Minta kredensial login ke pemilik project.

---

## Update Kode (Deployment Berikutnya)

### Jika pakai Cara A (build dari source):
```bash
cd /opt/manajemen-aset
git pull
docker compose -f docker-compose.prod.yml up -d --build
```

### Jika pakai Cara B (image dari registry):
```bash
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

---

## Catatan Penting

### Data Persisten
File upload dan database **tidak tersimpan di dalam container** — keduanya ada di Docker named volumes:
- `storage_data` — file upload (foto aset, dokumen, dll)
- Database ada di server database eksternal (bukan di VPS ini)

Volumes tidak terhapus saat container di-restart atau image diperbarui.

**Untuk backup file upload:**
```bash
docker run --rm -v manajemen-aset_storage_data:/data -v $(pwd):/backup \
  alpine tar czf /backup/storage-backup-$(date +%Y%m%d).tar.gz /data
```

### Melihat Log
```bash
# Semua service
docker compose -f docker-compose.prod.yml logs -f

# Service tertentu
docker compose -f docker-compose.prod.yml logs -f app
docker compose -f docker-compose.prod.yml logs -f worker
```

### Restart Aplikasi
```bash
docker compose -f docker-compose.prod.yml restart
```

### Stop Aplikasi
```bash
docker compose -f docker-compose.prod.yml down
```

### Troubleshooting Koneksi Database
Jika log menampilkan "Menunggu MySQL..." tanpa henti, berarti cloudflared tunnel gagal terhubung. Periksa:
1. `CF_ACCESS_CLIENT_ID` dan `CF_ACCESS_CLIENT_SECRET` sudah benar di `.env`
2. `CF_ACCESS_HOSTNAME` sudah benar
3. Server punya koneksi internet keluar (untuk terhubung ke Cloudflare)
