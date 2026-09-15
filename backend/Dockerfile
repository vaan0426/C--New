FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan config:clear

EXPOSE 10000

# Render provides $PORT at runtime. Uses a local SQLite file (no external
# DB credentials to manage for a preview deploy) — ephemeral, so it resets
# on every redeploy/restart; migrate/seed on boot keeps it in a known,
# always-populated state. Fine for a demo, not for real production traffic.
CMD mkdir -p database && touch database/database.sqlite \
    && php artisan migrate --force \
    && (php artisan db:seed --force || true) \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
