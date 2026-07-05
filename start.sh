#!/usr/bin/env bash
php artisan migrate --force --seed
php artisan storage:link || true
php-fpm -D
nginx -g "daemon off;"