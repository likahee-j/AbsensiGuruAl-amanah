#!/usr/bin/env bash
# Dijalankan SETIAP KALI Codespace di-start (termasuk setelah stop/restart).
# Tujuan: pastikan migration up-to-date kalau ada commit baru dengan migration tambahan.

set -euo pipefail

cd "$(dirname "$0")/.."

# Tunggu MySQL siap (kalau service belum healthy)
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

echo "✅ Codespace siap."
