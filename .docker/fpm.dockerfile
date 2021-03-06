FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql \
    && pecl install xdebug-2.9.2 \
    && docker-php-ext-enable xdebug

COPY php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app
