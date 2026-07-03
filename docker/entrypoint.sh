#!/bin/sh
set -e

# ─── 0. Cloudflare Zero Trust Tunnel (opsional) ─────────────────
# Aktif hanya jika CF_ACCESS_CLIENT_ID diset.
# Membuat proxy lokal 127.0.0.1:DB_PORT → database on-premise via CF tunnel.
if [ -n "${CF_ACCESS_CLIENT_ID}" ] && [ -n "${CF_ACCESS_CLIENT_SECRET}" ]; then
    echo "[entrypoint] Memulai cloudflared tunnel → ${CF_ACCESS_HOSTNAME}..."
    cloudflared access tcp \
        --hostname "${CF_ACCESS_HOSTNAME}" \
        --url "127.0.0.1:${DB_PORT:-3306}" \
        --service-token-id "${CF_ACCESS_CLIENT_ID}" \
        --service-token-secret "${CF_ACCESS_CLIENT_SECRET}" \
        --loglevel error &
    echo "[entrypoint] Menunggu tunnel siap (4 detik)..."
    sleep 4
fi

# ─── 1. Inisialisasi storage volume ─────────────────────────────
# Jika volume baru (kosong), seed dari default yang di-bake ke image.
# Ini hanya jalan sekali di deployment pertama.
if [ ! -f /var/www/html/storage/app/public/default.jpg ]; then
    echo "[entrypoint] Storage volume kosong — menyalin defaults dari image..."
    cp -rn /var/www/.storage-seed/. /var/www/html/storage/
    chown -R www-data:www-data /var/www/html/storage
    echo "[entrypoint] Storage diinisialisasi."
fi

# ─── 2. Tunggu MySQL siap ───────────────────────────────────────
echo "[entrypoint] Menunggu MySQL di ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php -r "
    try {
        new PDO(
            'mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE}',
            '${DB_USERNAME}',
            '${DB_PASSWORD}'
        );
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] MySQL siap."

# ─── 3. Setup awal (hanya di container app, bukan worker/scheduler) ──
# Worker & scheduler hanya butuh DB siap, bukan setup ulang tiap start.
if [ "$1" = "php-fpm" ]; then
    # Pastikan direktori framework selalu ada di volume (volume lama mungkin tidak punya)
    mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs
    chown -R www-data:www-data storage

    echo "[entrypoint] Menjalankan migrasi..."
    php artisan migrate --force

    echo "[entrypoint] Membuat storage symlink..."
    php artisan storage:link --force

    echo "[entrypoint] Caching config, routes, views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "[entrypoint] App siap."
    echo ""
    echo "  ╔══════════════════════════════════════════════════╗"
    echo "  ║  Jika deployment pertama, jalankan:             ║"
    echo "  ║  docker compose exec app php artisan db:seed    ║"
    echo "  ╚══════════════════════════════════════════════════╝"
    echo ""
fi

# ─── 4. Jalankan perintah yang diteruskan (php-fpm / queue:listen / dll.) ──
exec "$@"
