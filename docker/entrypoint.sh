#!/bin/sh
set -eu

cd /app

mkdir -p \
	storage/framework/cache/data \
	storage/framework/sessions \
	storage/framework/views \
	storage/logs \
	bootstrap/cache

if [ "${SKIP_CACHING:-0}" != "1" ]; then
	php artisan config:cache --ansi
	php artisan route:cache --ansi
	php artisan view:cache --ansi
fi

if [ "${SKIP_MIGRATIONS:-0}" != "1" ]; then
	php artisan migrate --force --ansi
fi

exec frankenphp run --config /etc/caddy/Caddyfile
