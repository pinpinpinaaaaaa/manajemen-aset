# ═══════════════════════════════════════════════════════════════
# Stage 1 – Build Vite assets (Node 22)
# ═══════════════════════════════════════════════════════════════
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install --prefer-offline

# vite.config.js + semua resource (CSS, JS, views — Tailwind v4 scan)
COPY vite.config.js .
COPY resources ./resources

RUN npm run build

# ═══════════════════════════════════════════════════════════════
# Stage 2 – PHP 8.2-FPM Runtime
# ═══════════════════════════════════════════════════════════════
FROM php:8.2-fpm AS runtime

# System libraries untuk PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libgd-dev \
        libicu-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libwebp-dev \
        libonig-dev \
        unzip \
        curl \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions (DomPDF + Intervention Image + Maatwebsite Excel)
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        intl \
        mbstring \
        xml \
        dom \
        exif \
        opcache

# cloudflared — Cloudflare Zero Trust tunnel client (akses DB on-premise)
RUN curl -fsSL https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 \
    -o /usr/local/bin/cloudflared \
    && chmod +x /usr/local/bin/cloudflared

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies dulu (layer tersendiri agar cache tidak rusak
# saat hanya kode app yang berubah)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Salin seluruh kode aplikasi
COPY . .

# Timpa dengan hasil Vite production build dari stage 1
COPY --from=assets /app/public/build ./public/build

# Finalkan autoloader setelah semua kode tersedia
RUN composer dump-autoload --optimize --classmap-authoritative

# Simpan copy storage defaults — dipakai entrypoint untuk seed volume kosong
RUN cp -r storage /var/www/.storage-seed

# Permissions: www-data harus bisa tulis ke storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html /var/www/.storage-seed \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

RUN echo "upload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 256M" \
    > /usr/local/etc/php/conf.d/uploads.ini

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

# ═══════════════════════════════════════════════════════════════
# Stage 3 – Nginx (static files + PHP-FPM proxy)
# ═══════════════════════════════════════════════════════════════
FROM nginx:1.26-alpine AS nginx-stage

# Ambil hasil build public/ dari stage runtime
# (sudah include public/build/ hasil Vite)
COPY --from=runtime /var/www/html/public /var/www/html/public
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
