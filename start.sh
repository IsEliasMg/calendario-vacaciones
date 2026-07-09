#!/usr/bin/env bash
set -e

mkdir -p database \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache \
  storage/logs \
  bootstrap/cache

touch database/database.sqlite || true
chmod -R 775 storage bootstrap/cache database || true

# Si no hay APP_KEY, generar una temporal (mejor definirla en Variables)
if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY no está definida. Generando una temporal..."
  export APP_KEY="$(php artisan key:generate --show --no-ansi 2>/dev/null || true)"
fi

php artisan config:clear || true
php artisan migrate --force
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
