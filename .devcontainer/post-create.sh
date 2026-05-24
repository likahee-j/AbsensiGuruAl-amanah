#!/usr/bin/env bash
# Dijalankan SEKALI saat Codespace pertama dibuat.
# Tujuan: setup Laravel agar siap dipakai (composer, npm, .env, key, migrate, build).

set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> [0/7] Pastikan PHP extension gd & zip terinstall"
# Image PHP devcontainer kadang punya yarn.list dengan GPG expired
# yang bikin apt-get update gagal. Kita tidak pakai Yarn, jadi disable.
if [ -f /etc/apt/sources.list.d/yarn.list ]; then
    sudo rm -f /etc/apt/sources.list.d/yarn.list
fi
if ! php -m | grep -q '^gd$'; then
    # install-php-extensions adalah helper script mlocati yang otomatis handle
    # OS lib + docker-php-ext-configure + docker-php-ext-install.
    if ! command -v install-php-extensions >/dev/null 2>&1; then
        sudo curl -sSLf -o /usr/local/bin/install-php-extensions \
            https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions
        sudo chmod +x /usr/local/bin/install-php-extensions
    fi
    # Image MS devcontainer tidak set PHP_INI_DIR; harus disuplai eksplisit
    # supaya docker-php-ext-enable bisa menulis .ini ke lokasi yang benar.
    sudo PHP_INI_DIR=/usr/local/etc/php install-php-extensions gd zip || true

    # Fallback: kalau install-php-extensions gagal di langkah enable,
    # buat file .ini manual selama .so-nya sudah ter-compile.
    EXT_DIR=$(php -r 'echo ini_get("extension_dir");')
    if ! php -m | grep -q '^gd$' && [ -f "$EXT_DIR/gd.so" ]; then
        sudo bash -c "echo 'extension=gd' > /usr/local/etc/php/conf.d/docker-php-ext-gd.ini"
    fi
    if ! php -m | grep -q '^zip$' && [ -f "$EXT_DIR/zip.so" ]; then
        sudo bash -c "echo 'extension=zip' > /usr/local/etc/php/conf.d/docker-php-ext-zip.ini"
    fi
fi

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
