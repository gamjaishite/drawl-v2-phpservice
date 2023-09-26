FROM php:8.0-apache

RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql
COPY ./php.ini /usr/local/etc/php/php.ini
RUN a2enmod rewrite