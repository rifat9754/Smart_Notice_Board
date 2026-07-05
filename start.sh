#!/bin/sh

php artisan migrate --force --seed
php artisan storage:link || true
php-fpm -D
exec nginx -g "daemon off;"