FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan config:clear

EXPOSE 10000

# Render provides $PORT at runtime; migrate/seed on boot so the demo
# database is always in a known state, then serve with the built-in
# dev server (fine for a preview deploy, not for real production traffic).
CMD php artisan migrate --force \
    && (php artisan db:seed --force || true) \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
