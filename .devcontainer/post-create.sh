#!/usr/bin/env bash
# Dijalankan SEKALI saat Codespace pertama dibuat.
# Tujuan: setup Laravel agar siap dipakai (composer, npm, .env, key, migrate, build).

set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> [1/7] Install PHP dependencies (composer)"
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "==> [2/7] Install Node dependencies (npm)"
npm ci

echo "==> [3/7] Siapkan .env (jika belum ada)"
if [ ! -f .env ]; then
    cp .env.example .env
    # Override koneksi DB ke service MySQL devcontainer
    sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=mysql|' .env
    sed -i 's|^# DB_HOST=.*|DB_HOST=mysql|' .env
    sed -i 's|^# DB_PORT=.*|DB_PORT=3306|' .env
    sed -i 's|^# DB_DATABASE=.*|DB_DATABASE=absensi|' .env
    sed -i 's|^# DB_USERNAME=.*|DB_USERNAME=laravel|' .env
    sed -i 's|^# DB_PASSWORD=.*|DB_PASSWORD=secret|' .env
fi

echo "==> [4/7] Generate APP_KEY (kalau belum ada)"
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

echo "==> [5/7] Symlink storage publik"
php artisan storage:link || true

echo "==> [6/7] Jalankan migration + seeder"
php artisan migrate --seed --force

echo "==> [7/7] Build asset frontend"
npm run build

echo ""
echo "✅ Setup selesai. Jalankan: composer run dev"
echo "   atau:                   php artisan serve --host=0.0.0.0"
echo ""
echo "Login default:"
echo "  Admin   : admin@sekolah.sch.id / password123"
echo "  Kepsek  : kepsek@sekolah.sch.id / password123"
echo "  Guru    : hafshah.purnawati@sekolah.sch.id / password123 (dst.)"
