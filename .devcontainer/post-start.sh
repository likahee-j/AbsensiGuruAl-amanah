#!/usr/bin/env bash
# Dijalankan SETIAP KALI Codespace di-start (termasuk setelah stop/restart).
# Tujuan:
#  1. Pastikan migration up-to-date kalau ada commit baru dengan migration tambahan.
#  2. Set APP_URL ke domain publik Codespaces (supaya route()/asset() pakai HTTPS).
#  3. Jalankan `php artisan serve` di background biar port 8000 langsung siap di-share.

set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Memastikan MySQL siap..."
for i in {1..20}; do
    if php -r "try { new PDO('mysql:host=mysql;port=3306;dbname=absensi', 'laravel', 'secret'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
        echo "    MySQL OK."
        break
    fi
    sleep 1
done

echo "==> Cek migration tertunda..."
php artisan migrate --force --no-interaction || true

if [ -n "${CODESPACE_NAME:-}" ] && [ -n "${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-}" ]; then
    PUBLIC_URL="https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    echo "==> Set APP_URL ke ${PUBLIC_URL}"
    if grep -q '^APP_URL=' .env; then
        sed -i "s|^APP_URL=.*|APP_URL=${PUBLIC_URL}|" .env
    else
        echo "APP_URL=${PUBLIC_URL}" >> .env
    fi
    if grep -q '^ASSET_URL=' .env; then
        sed -i "s|^ASSET_URL=.*|ASSET_URL=${PUBLIC_URL}|" .env
    else
        echo "ASSET_URL=${PUBLIC_URL}" >> .env
    fi
    php artisan config:clear || true
fi

echo "==> Hentikan server lama (kalau ada)..."
pkill -f "artisan serve" || true
sleep 1

echo "==> Start Laravel server di background (port 8000)..."
mkdir -p storage/logs
nohup php artisan serve --host=0.0.0.0 --port=8000 \
    > storage/logs/artisan-serve.log 2>&1 &
disown || true

sleep 2
if pgrep -f "artisan serve" >/dev/null; then
    echo "✅ Codespace siap. Server jalan di port 8000."
    if [ -n "${CODESPACE_NAME:-}" ]; then
        echo "   URL publik: https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    fi
else
    echo "⚠️  Server gagal start. Cek storage/logs/artisan-serve.log"
fi
