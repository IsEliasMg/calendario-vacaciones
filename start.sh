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

if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY no está definida. Generando una temporal..."
  export APP_KEY="$(php artisan key:generate --show --no-ansi 2>/dev/null || true)"
fi

php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

if [ "${RESET_DB:-false}" = "true" ]; then
  echo "RESET_DB=true → recreando base de datos vacía..."
  php artisan migrate:fresh --seed --force
else
  php artisan migrate --force
  # Solo seed inicial si aún no hay settings (evita resetear colores/datos)
  php artisan db:seed --force --class=Database\\Seeders\\DatabaseSeeder || true
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
