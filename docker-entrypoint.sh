#!/bin/sh
set -e

echo "Ajustando permissões de storage e bootstrap/cache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"