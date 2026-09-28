# syntax=docker/dockerfile:1
# Production image: FrankenPHP (Caddy) on :8080 with zstd/gzip and immutable
# static caching. Disk is ephemeral: point DB_URL at a real database.
#   docker build -t app . && docker run -p 8080:8080 --env-file .env app

FROM node:22-bookworm AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
# Fonts download from Google here and ship self-hosted in public/build.
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

FROM dunglas/frankenphp:1-php8.4-bookworm

WORKDIR /app

RUN install-php-extensions pdo_pgsql pdo_sqlite gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

COPY --from=vendor /app/vendor /app/vendor
COPY . /app/
COPY --from=assets /build/public/build /app/public/build

ENV APP_ENV=production \
	APP_DEBUG=false \
	LOG_CHANNEL=stderr \
	LOG_STACK=stderr \
	SERVER_NAME=:8080

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
	&& composer dump-autoload --optimize --no-dev --no-interaction \
	&& php artisan package:discover --ansi \
	&& rm /usr/bin/composer

EXPOSE 8080

# The base image probes :2019/metrics, but the Caddyfile turns the admin API off.
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
	CMD curl -fsS http://127.0.0.1:8080/up || exit 1

ENTRYPOINT ["entrypoint"]
