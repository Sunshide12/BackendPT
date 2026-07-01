# ── Stage: PHP 8.2 + extensiones mínimas para Laravel ────────────────────────
FROM php:8.4-fpm-alpine

# ── Dependencias del sistema ──────────────────────────────────────────────────
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        postgresql-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
    && rm -rf /var/cache/apk/*

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Directorio de trabajo ─────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── 1) Instalar dependencias ANTES de copiar el resto del código ──────────────
#    → Si solo cambia código fuente, esta capa queda cacheada.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --optimize-autoloader

# ── 2) Copiar el código fuente ────────────────────────────────────────────────
COPY . .

# ── 3) Autoloader final + optimizaciones ──────────────────────────────────────
RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ── Nginx ─────────────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Supervisor (gestiona nginx + php-fpm en un solo contenedor) ───────────────
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8000

# Reemplaza __PORT__ en nginx por la variable $PORT de Vercel (o 8000 por defecto) y arranca
CMD sed -i "s/__PORT__/${PORT:-8000}/g" /etc/nginx/http.d/default.conf && /usr/bin/supervisord -c /etc/supervisord.conf