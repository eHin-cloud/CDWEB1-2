#!/usr/bin/env sh
set -e

cd /var/www/html

# Kiem tra neu lenh truyen vao la khoi dong web server (php artisan serve hoac mac dinh)
is_server_cmd=0
if [ "$#" -eq 0 ] || [ "$1" = "php" -a "$2" = "artisan" -a "$3" = "serve" ]; then
    is_server_cmd=1
fi

# Chi thuc hien cac buoc setup ung dung neu co ma nguon (ton tai artisan) va dang bat web server
if [ -f artisan ] && [ "$is_server_cmd" -eq 1 ]; then
    if [ ! -f .env ]; then
        if [ -f .env.docker ]; then
            cp .env.docker .env
        elif [ -f .env.example ]; then
            cp .env.example .env
        fi
    fi

    if [ ! -f vendor/autoload.php ] && [ -f composer.json ]; then
        composer install --no-interaction --prefer-dist
    fi

    if [ -f package.json ] && [ ! -d node_modules ]; then
        npm install
    fi

    if [ -f .env ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
        php artisan key:generate --force
    fi

    php artisan config:clear || true

    if [ "${DB_CONNECTION:-}" = "mysql" ]; then
        retries=0
        until mysqladmin ping -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-smartroom}" -p"${DB_PASSWORD:-smartroom}" --skip-ssl --silent 2>/dev/null || [ "$retries" -ge 15 ]; do
            retries=$((retries + 1))
            sleep 2
        done
        if [ "$retries" -lt 15 ]; then
            php artisan migrate --force || true
        fi
    else
        php artisan migrate --force || true
    fi

    # Khoi dong Reverb WebSocket Server chay nen
    php artisan reverb:start --host=0.0.0.0 --port=8085 &
fi

exec "$@"
