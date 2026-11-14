#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

: "${DEPLOY_SHA:?DEPLOY_SHA is required}"

project="$HOME/Dev/aloha-app"
backup_root="$HOME/Dev/aloha-app-backups"
stamp="$(date +%Y%m%d-%H%M%S)"
backup="$backup_root/$stamp"
staging="$HOME/Dev/aloha-app.next-$stamp"
rollback="$HOME/Dev/aloha-app.rollback-$stamp"
test_name="aloha-app-restore-check-$stamp"
maintenance=0
services_stopped=0
swapped=0

compose_in() {
    local directory="$1"
    shift
    (cd "$directory" && APP_PORT=5310 docker compose --project-name aloha-app "$@")
}

compose_exec() {
    local directory="$1"
    local service="$2"
    shift 2
    compose_in "$directory" exec --interactive=false -T "$service" "$@"
}

cleanup() {
    local status=$?
    docker rm -f "$test_name" >/dev/null 2>&1 || true
    if [ "$status" -ne 0 ] && [ "$maintenance" -eq 1 ] && [ "$services_stopped" -eq 0 ] && [ "$swapped" -eq 0 ]; then
        compose_exec "$project" app php artisan up >/dev/null 2>&1 || true
    fi
    if [ "$swapped" -eq 0 ] && [ -d "$staging" ]; then
        rm -rf "$staging"
    fi
    return "$status"
}
trap cleanup EXIT

test -d "$project/.volumes/db"
test -d "$project/.volumes/app"
test -f "$project/.env"
app_env="$(sed -n 's/^APP_ENV=//p' "$project/.env" | tail -1 | tr -d "\"'")"
if [ "$app_env" != "production" ]; then
    echo "Refusing production deploy because APP_ENV is not production." >&2
    exit 1
fi
mkdir -p "$backup_root" "$backup"
chmod 700 "$backup_root" "$backup"

old_image="$(docker inspect aloha-app-app-1 --format '{{.Image}}')"
docker tag "$old_image" "aloha-app:rollback-$stamp"

# Prepare and build the exact commit before interrupting production traffic.
git clone --branch main https://github.com/alisonsarto/aloha-app.git "$staging"
git -C "$staging" checkout --detach "$DEPLOY_SHA"
cp -p "$project/.env" "$backup/.env"
cp -p "$project/.env" "$staging/.env"
chmod 600 "$backup/.env" "$staging/.env"
compose_in "$staging" config --quiet
compose_in "$staging" build app

# Keep the backup and restore verification inside the maintenance window so
# no order or customer update can land after the database snapshot.
compose_exec "$project" app php artisan down --retry=60
maintenance=1
db_name="$(docker exec aloha-app-db-1 sh -c 'printf %s "$MYSQL_DATABASE"')"
test -n "$db_name"

docker exec aloha-app-db-1 sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysqldump --single-transaction --no-tablespaces --routines --triggers --events --hex-blob --set-gtid-purged=OFF -h 127.0.0.1 -P 3306 -u"$MYSQL_USER" "$MYSQL_DATABASE"' | gzip -1 > "$backup/database.sql.gz"
gzip -t "$backup/database.sql.gz"

test_password="$(openssl rand -hex 24)"
docker run -d --rm --name "$test_name" \
    -e MYSQL_ROOT_PASSWORD="$test_password" \
    -e MYSQL_DATABASE="$db_name" \
    mysql:8.4 >/dev/null
docker exec "$test_name" sh -c 'umask 077; printf "[client]\nuser=root\npassword=%s\n" "$MYSQL_ROOT_PASSWORD" > /tmp/restore-test.cnf'

ready=0
for attempt in $(seq 1 90); do
    if docker exec "$test_name" sh -c 'mysql --defaults-extra-file=/tmp/restore-test.cnf --batch --skip-column-names "$MYSQL_DATABASE" -e "SELECT 1"' >/dev/null 2>&1; then
        ready=1
        break
    fi
    sleep 1
done
if [ "$ready" -ne 1 ]; then
    echo "Temporary MySQL authentication did not become ready." >&2
    exit 1
fi

gzip -dc "$backup/database.sql.gz" | docker exec -i "$test_name" sh -c 'exec mysql --defaults-extra-file=/tmp/restore-test.cnf "$MYSQL_DATABASE"'
table_count="$(docker exec "$test_name" sh -c 'mysql --defaults-extra-file=/tmp/restore-test.cnf --batch --skip-column-names "$MYSQL_DATABASE" -e "SHOW TABLES"' | wc -l)"
if [ "$table_count" -lt 1 ]; then
    echo "Database restore test produced no tables." >&2
    exit 1
fi
cleanup

# Stop cleanly before archiving the raw MySQL data directory.
compose_in "$project" stop app db
services_stopped=1
docker run --rm --entrypoint sh \
    -v "$project/.volumes:/source:ro" \
    -v "$backup:/backup" \
    -e HOST_UID="$(id -u)" -e HOST_GID="$(id -g)" \
    mysql:8.4 -c 'tar -czf /backup/volumes.tar.gz -C /source . && chown "$HOST_UID:$HOST_GID" /backup/volumes.tar.gz && chmod 600 /backup/volumes.tar.gz'
docker run --rm --entrypoint sh -v "$backup:/backup:ro" mysql:8.4 \
    -c 'tar -tzf /backup/volumes.tar.gz >/dev/null'
chmod 600 "$backup/database.sql.gz"
sha256sum "$backup/database.sql.gz" "$backup/volumes.tar.gz" "$backup/.env"
echo "Verified SQL restore: $table_count tables. Backup: $backup"

# Keep the existing bind-mounted database and storage data at the same path.
mv "$project/.volumes" "$staging/.volumes"
mv "$project" "$rollback"
mv "$staging" "$project"
swapped=1

# APP_ENV=local seeding is disabled for production in the new entrypoint.
compose_in "$project" up -d --no-build

ready=0
for attempt in $(seq 1 90); do
    if compose_exec "$project" app php artisan migrate:status >/dev/null 2>&1; then
        ready=1
        break
    fi
    sleep 2
done
if [ "$ready" -ne 1 ]; then
    echo "Deployment did not become ready; the app remains in maintenance mode." >&2
    echo "Verified backup: $backup" >&2
    echo "Previous code: $rollback" >&2
    compose_in "$project" logs --tail=100 app >&2 || true
    exit 1
fi

compose_exec "$project" app php artisan up
curl --fail --silent --show-error --max-time 15 http://127.0.0.1:5310/ -o /dev/null

echo "Deployment succeeded: $DEPLOY_SHA"
echo "Verified backup: $backup"
echo "Previous code: $rollback"
