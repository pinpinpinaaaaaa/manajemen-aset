#!/usr/bin/env bash
# =============================================================================
# scripts/predeploy-check.sh
# Jalankan sebelum setiap deploy ke production.
# Exit 1 jika ada yang gagal.
# =============================================================================

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

PASS=0
FAIL=0

ok()   { echo -e "${GREEN}  ✓ $1${NC}"; PASS=$((PASS+1)); }
fail() { echo -e "${RED}  ✗ $1${NC}"; FAIL=$((FAIL+1)); }
info() { echo -e "${YELLOW}  → $1${NC}"; }

echo ""
echo "============================================="
echo "  Pre-Deploy Check — Sistem Manajemen Aset"
echo "============================================="
echo ""

# ── 1. php artisan route:cache ─────────────────────────────────────────────
info "Menguji route:cache..."
if php artisan route:cache --quiet 2>/dev/null; then
    ok "route:cache berhasil (tidak ada route closure)"
else
    fail "route:cache GAGAL — cek apakah ada route closure di routes/web.php"
fi

# ── 2. php artisan config:cache ────────────────────────────────────────────
info "Menguji config:cache..."
if php artisan config:cache --quiet 2>/dev/null; then
    ok "config:cache berhasil"
else
    fail "config:cache GAGAL — cek config/*.php"
fi

# ── 3. php artisan view:cache ──────────────────────────────────────────────
info "Menguji view:cache..."
if php artisan view:cache --quiet 2>/dev/null; then
    ok "view:cache berhasil"
else
    fail "view:cache GAGAL — cek syntax Blade di resources/views/"
fi

# ── 4. php artisan route:list (tidak ada error) ────────────────────────────
info "Menguji route:list..."
if php artisan route:list --quiet 2>/dev/null; then
    ok "route:list tidak ada error"
else
    fail "route:list GAGAL — ada route atau controller yang rusak"
fi

# ── 5. composer dump-autoload --optimize --classmap-authoritative ──────────
info "Menguji composer dump-autoload (--classmap-authoritative seperti production)..."
if composer dump-autoload --optimize --classmap-authoritative --quiet 2>/dev/null; then
    ok "composer dump-autoload berhasil"
else
    fail "composer dump-autoload GAGAL — cek composer.json"
fi

# ── 6. Class yang di-use tapi tidak ada di codebase ───────────────────────
info "Memeriksa use statement yang mengarah ke class yang tidak ada..."
MISSING_CLASSES=0
while IFS= read -r line; do
    # Ekstrak nama class dari 'use App\...' statement
    CLASS=$(echo "$line" | grep -oP 'use\s+\K(App\\[A-Za-z\\]+)' | head -1)
    if [ -n "$CLASS" ]; then
        # Konversi namespace ke path file
        FILE_PATH=$(echo "$CLASS" | sed 's/\\\\/\//g' | sed 's/App\//app\//' ).php
        if [ ! -f "$FILE_PATH" ]; then
            fail "Class tidak ditemukan: $CLASS (dari: $line)"
            MISSING_CLASSES=$((MISSING_CLASSES+1))
        fi
    fi
done < <(grep -rn "^use App\\" app/ routes/ --include="*.php" | grep -v "vendor/" | grep -v ".git/")

if [ "$MISSING_CLASSES" -eq 0 ]; then
    ok "Semua use statement App\\ valid (class ditemukan di filesystem)"
fi

# ── 7. php artisan rekap:bulanan (dry-run: bulan berjalan harus error) ─────
info "Menguji rekap:bulanan (bulan berjalan harus ditolak)..."
CURRENT_MONTH=$(date +%Y-%m)
REKAP_OUTPUT=$(php artisan rekap:bulanan "$CURRENT_MONTH" 2>&1 || true)
if echo "$REKAP_OUTPUT" | grep -q "sudah selesai"; then
    ok "rekap:bulanan menolak bulan berjalan dengan benar"
else
    fail "rekap:bulanan tidak memvalidasi bulan dengan benar: $REKAP_OUTPUT"
fi

# ── 8. Cek tidak ada route closure ─────────────────────────────────────────
info "Memeriksa route closure di routes/web.php..."
CLOSURE_COUNT=$(grep -cP "Route::(?:get|post|put|patch|delete|any)\s*\([^,]+,\s*(?:fn\s*\(|function\s*\()" routes/web.php 2>/dev/null || true)
if [ "$CLOSURE_COUNT" -eq 0 ]; then
    ok "Tidak ada route closure (route:cache-safe)"
else
    fail "Ditemukan $CLOSURE_COUNT route closure di routes/web.php — ganti dengan controller"
fi

# ── 9. Cek env() di luar config/ ──────────────────────────────────────────
info "Memeriksa env() di luar folder config/ ..."
ENV_OUTSIDE=$(grep -rn "env(" app/ routes/ resources/ database/ --include="*.php" 2>/dev/null | grep -v "#" | grep -v "//" | wc -l || true)
if [ "$ENV_OUTSIDE" -eq 0 ]; then
    ok "Tidak ada env() di luar config/ (config:cache-safe)"
else
    fail "Ditemukan $ENV_OUTSIDE panggilan env() di luar config/ — akan null setelah config:cache"
    grep -rn "env(" app/ routes/ resources/ database/ --include="*.php" 2>/dev/null | grep -v "#" | grep -v "//" | head -10
fi

# ── 10. php artisan test ───────────────────────────────────────────────────
info "Menjalankan test suite..."
if php artisan test --stop-on-failure 2>/dev/null; then
    ok "Semua test lulus"
else
    fail "Ada test yang gagal — periksa output di atas"
fi

# ── Ringkasan ──────────────────────────────────────────────────────────────
echo ""
echo "============================================="
if [ "$FAIL" -eq 0 ]; then
    echo -e "${GREEN}  LULUS: $PASS pengecekan berhasil. Siap deploy!${NC}"
else
    echo -e "${RED}  GAGAL: $FAIL dari $((PASS+FAIL)) pengecekan. Perbaiki dulu sebelum deploy.${NC}"
    echo ""
    exit 1
fi
echo "============================================="
echo ""
