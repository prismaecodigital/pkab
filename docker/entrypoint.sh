#!/bin/sh
set -e

cd /var/www/html

# A named volume mounted over storage/ starts empty, and .dockerignore keeps the
# directories out of the build context, so recreate them on every start.
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

# Development mounts the source tree; vendor/ may not exist yet on a fresh clone.
if [ ! -f vendor/autoload.php ]; then
    echo "vendor/ is missing — running composer install"
    composer install --no-interaction --prefer-dist
    chown -R www-data:www-data vendor
fi

if [ ! -L public/storage ]; then
    su-exec www-data php artisan storage:link >/dev/null 2>&1 || true
fi

if [ "$APP_ENV" = "production" ]; then
    if [ -z "$APP_KEY" ]; then
        echo "APP_KEY is empty — refusing to start in production" >&2
        exit 1
    fi

    case "$APP_URL" in
        http://*|https://*) ;;
        *)
            echo "APP_URL must include its scheme (e.g. https://pkab.example.com), got '$APP_URL'" >&2
            exit 1
            ;;
    esac

    # Compiled Blade views live in the persistent storage volume; Docker keeps
    # source mtimes, so a view from a previous release can look fresh forever.
    su-exec www-data php artisan optimize:clear

    if [ "$CACHE_CONFIG" = "true" ]; then
        su-exec www-data php artisan optimize
    fi
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
    su-exec www-data php artisan migrate --force
fi

exec "$@"
