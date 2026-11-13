#!/bin/sh
set -eu

echo "Aguardando o banco de dados em ${DB_HOST}:${DB_PORT}..."

# The host bind mount can be empty on first startup and hides the image's
# pre-created storage directories. Recreate Laravel's runtime paths in it.
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage

if [ ! -f vendor/autoload.php ]; then
    echo "Instalando dependências PHP..."
    for attempt in 1 2 3; do
        if composer install --no-interaction --prefer-dist --optimize-autoloader; then
            break
        fi
        if [ "$attempt" -eq 3 ]; then
            echo "Não foi possível instalar as dependências PHP após 3 tentativas." >&2
            exit 1
        fi
        echo "Composer falhou; nova tentativa em 5 segundos (${attempt}/3)..." >&2
        rm -rf vendor
        sleep 5
    done
fi

until php -r '$connection = @fsockopen(getenv("DB_HOST"), (int) getenv("DB_PORT"), $error, $message, 1); exit($connection ? 0 : 1);'; do
    sleep 2
done

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php artisan key:generate --show)"
fi

php artisan migrate --force

# Seeders contain development defaults, including demo pricing and an admin
# account. Never run them implicitly on a production restart or deployment.
if [ "${APP_ENV:-local}" = "local" ]; then
    php artisan db:seed --force
fi

php artisan optimize:clear

exec "$@"
