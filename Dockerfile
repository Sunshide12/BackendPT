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
# Solo el autoloader → las caches se generan en runtime con las vars reales
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ── Nginx ─────────────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Supervisor (gestiona nginx + php-fpm en un solo contenedor) ───────────────
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8000

# Script de arranque: aplica el puerto dinámico, caches de Laravel con vars reales, y lanza supervisor
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]