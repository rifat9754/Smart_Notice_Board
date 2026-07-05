FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpq-dev libzip-dev libonig-dev libxml2-dev \
    nginx \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

COPY docker/nginx/default.conf /etc/nginx/sites-available/default

EXPOSE 80

RUN chmod +x /var/www/html/start.sh

CMD ["/var/www/html/start.sh"]