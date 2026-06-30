#!/bin/bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "Membuat .env dari .env.example..."
    cp .env.example .env
fi

echo "Generate APP_KEY..."
php artisan key:generate --force

echo "Menunggu MySQL..."
DB_HOST="${DB_HOST:-mysql}"
DB_DATABASE="${DB_DATABASE:-pmu_db}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-root}"
until php -r "new PDO('mysql:host=$DB_HOST;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');" 2>/dev/null; do
    sleep 2
done

echo "Menjalankan migration..."
php artisan migrate --force

echo "Membersihkan cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "Membuat storage link..."
php artisan storage:link --force || true

echo "Memulai scheduler..."
php artisan schedule:work &>/dev/null &

echo "Memulai PHP-FPM..."
exec php-fpm
