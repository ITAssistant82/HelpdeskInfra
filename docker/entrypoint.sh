#!/bin/bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "Membuat .env dari .env.example..."
    cp .env.example .env
fi

normalize_url_env() {
    local var_name="$1"
    local value="$2"

    # Perbaiki typo umum: https:/domain -> https://domain
    if [[ "$value" =~ ^https:/[^/] ]]; then
        value="${value/https:\//https://}"
    fi

    if [[ "$value" =~ ^http:/[^/] ]]; then
        value="${value/http:\//http://}"
    fi

    if [ "$var_name" = "APP_URL" ]; then
        # APP_URL sebaiknya tanpa trailing slash
        value="${value%/}"

        # Validasi ketat untuk mencegah redirect URL rusak
        if [[ ! "$value" =~ ^https?://[^/]+$ ]]; then
            echo "ERROR: APP_URL tidak valid: '$value'"
            echo "Gunakan format: https://domain.tld"
            exit 1
        fi
    fi

    echo "$value"
}

echo "Overwrite .env dengan nilai dari environment Docker..."
for var in APP_URL APP_ENV APP_DEBUG DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD MICROSOFT_REDIRECT_URI MICROSOFT_CLIENT_ID MICROSOFT_CLIENT_SECRET MICROSOFT_TENANT_ID CACHE_STORE; do
    if [ -n "${!var}" ]; then
        value="${!var}"
        if [ "$var" = "APP_URL" ] || [ "$var" = "MICROSOFT_REDIRECT_URI" ]; then
            value="$(normalize_url_env "$var" "$value")"
        fi

        # Hapus baris yang sudah ada, lalu append nilai baru
        sed -i "/^${var}=/d" .env
        echo "${var}=${value}" >> .env
    fi
done

echo "Generate APP_KEY (hanya jika belum ada)..."
php artisan key:generate

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
