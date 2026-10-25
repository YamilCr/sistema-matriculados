#!/bin/sh
# Arranque de la demo: base SQLite nueva + datos de ejemplo en cada inicio.
set -e
cd /app

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database
touch database/database.sqlite
export DB_DATABASE=/app/database/database.sqlite

# Clave de la app: si no viene definida, se genera una (las sesiones se reinician con la demo)
export APP_KEY="${APP_KEY:-$(php artisan key:generate --show)}"
# Render expone la URL pública en RENDER_EXTERNAL_URL
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:${PORT:-10000}}}"

php artisan migrate:fresh --seed --force
php artisan storage:link --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Servidor embebido de PHP con el router de Laravel (conserva las variables de entorno).
# Debe correr desde public/, igual que "php artisan serve".
cd public
exec php -S 0.0.0.0:"${PORT:-10000}" ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
