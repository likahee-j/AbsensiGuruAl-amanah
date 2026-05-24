#!/usr/bin/env bash
# Dijalankan SETIAP KALI Codespace di-start (termasuk setelah stop/restart).
# Tujuan:
#  1. Pastikan migration up-to-date kalau ada commit baru dengan migration tambahan.
#  2. Set APP_URL ke domain publik Codespaces (supaya route()/asset() pakai HTTPS).
#
# Catatan: `php artisan serve` TIDAK dijalankan di sini lagi karena proses
# background dari postStartCommand sering ke-reap oleh container init di
# Codespaces. Server di-start via .vscode/tasks.json (runOn: folderOpen),
# yang dipegang VSCode jadi proses tetap hidup selama tab Codespace terbuka.

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

echo "✅ Codespace siap. Server otomatis di-start oleh VSCode task 'Laravel: Serve'."
echo "   Cek tab TERMINAL → pilih dropdown 'Laravel: Serve' untuk lihat lognya."
if [ -n "${CODESPACE_NAME:-}" ]; then
    echo "   URL publik: https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
fi
