#!/bin/sh
set -e

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

if [ "${RUN_MIGRATIONS_ON_START:-0}" = "1" ]; then
  echo "Rodando migrations..."
  su -s /bin/sh www-data -c "php /var/www/html/artisan migrate --force"
fi

if [ "${RUN_ARTISAN_OPTIMIZE:-1}" = "1" ]; then
  echo "Otimização Laravel..."
  su -s /bin/sh www-data -c "php /var/www/html/artisan config:cache"
  su -s /bin/sh www-data -c "php /var/www/html/artisan route:cache"
  su -s /bin/sh www-data -c "php /var/www/html/artisan view:cache"
fi

exec "$@"