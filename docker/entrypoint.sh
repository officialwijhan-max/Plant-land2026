#!/bin/sh
set -e
cd /var/www/html

# Volumes mounted over storage/ and public/uploads start out empty.
mkdir -p storage/app/public storage/fonts storage/logs \
         storage/framework/cache storage/framework/sessions \
         storage/framework/testing storage/framework/views \
         bootstrap/cache public/uploads/settings

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generate one with: php artisan key:generate --show" >&2
    exit 1
fi

# Wait for the database (docker-compose starts it in parallel).
if [ -n "$DB_HOST" ]; then
    i=0
    until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT")?:3306), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Exception $e) { exit(1); }' 2>/dev/null; do
        i=$((i+1))
        [ "$i" -ge 30 ] && { echo "Database is not reachable at $DB_HOST" >&2; exit 1; }
        sleep 2
    done
fi

[ -e public/storage ] || php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi
if [ "${RUN_SEEDER:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan optimize:clear || true
chown -R www-data:www-data storage bootstrap/cache public/uploads

exec docker-php-entrypoint "$@"
