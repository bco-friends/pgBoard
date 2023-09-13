FROM php:8.2-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get -y update \
    && apt-get install -y build-essential git zip unzip autoconf postgresql libpq-dev less

RUN docker-php-ext-install pgsql pdo_pgsql

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
