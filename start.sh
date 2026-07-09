#!/usr/bin/env bash
set -e

mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache
touch database/database.sqlite || true
chmod -R 775 storage bootstrap/cache database || true

php artisan config:clear || true
php artisan migrate --force
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
