FROM php:7.4-cli

RUN apt-get update && apt-get install -y libpq-dev unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql \
    && pecl install xdebug-2.9.2 \
    && docker-php-ext-enable xdebug \
    #Installing Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


COPY php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app
