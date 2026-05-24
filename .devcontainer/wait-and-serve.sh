#!/usr/bin/env bash
# Dijalankan oleh .vscode/tasks.json (runOn: folderOpen).
# Tugasnya: tunggu prerequisites siap (composer install + .env + DB),
# baru exec `php artisan serve`. Ini cegah race condition di mana task
# Codespace jalan duluan sebelum postCreateCommand kelar.

set -euo pipefail
cd "$(dirname "$0")/.."

echo "==> Menunggu composer install selesai (vendor/autoload.php)..."
for i in {1..180}; do
    if [ -f vendor/autoload.php ]; then
        echo "    vendor OK."
        break
    fi
    sleep 2
done
if [ ! -f vendor/autoload.php ]; then
    echo "❌ vendor/autoload.php belum ada setelah 6 menit. Cek log post-create."
    exit 1
fi

echo "==> Menunggu .env tersedia..."
for i in {1..30}; do
    if [ -f .env ]; then
        echo "    .env OK."
        break
    fi
    sleep 1
done

# Kalau di Codespaces (DB pakai MySQL service), tunggu MySQL siap juga
# supaya request pertama gak gagal di middleware session.
if grep -q '^DB_CONNECTION=mysql' .env 2>/dev/null; then
    echo "==> Menunggu MySQL siap..."
    for i in {1..30}; do
        if php -r "try { new PDO('mysql:host=mysql;port=3306;dbname=absensi', 'laravel', 'secret'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
            echo "    MySQL OK."
            break
        fi
        sleep 2
    done
fi

echo "==> Start: php artisan serve --host=0.0.0.0 --port=8000"
exec php artisan serve --host=0.0.0.0 --port=8000
