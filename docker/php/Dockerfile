FROM php:8.2-fpm

RUN apt-get -y update \
    && apt-get install -y build-essential git zip unzip autoconf postgresql libpq-dev less

RUN docker-php-ext-install pgsql pdo_pgsql

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./conf/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
