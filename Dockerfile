FROM php:8.0-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql