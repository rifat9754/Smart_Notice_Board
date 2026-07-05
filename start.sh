#!/usr/bin/env bash


chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

php artisan migrate --force --seed
php artisan storage:link || true

php-fpm -D
nginx -g "daemon off;"