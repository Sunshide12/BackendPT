#!/bin/sh
set -e

# ── 1. Puerto dinámico (Render/Vercel inyectan $PORT) ─────────────────────────
sed -i "s/__PORT__/${PORT:-8000}/g" /etc/nginx/http.d/default.conf

# ── 2. Migraciones automáticas en cada deploy ─────────────────────────────────
php artisan migrate --force

# Borrar tablas y volver a crear
php artisan migrate:refresh --seed

# ── 3. Caches de Laravel con las variables de entorno reales de producción ─────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 4. Lanzar Supervisor (nginx + php-fpm) ────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisord.conf
