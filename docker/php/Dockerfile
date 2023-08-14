FROM php:7.0-fpm-alpine

RUN set -ex \
    && apk --no-cache add postgresql-dev bash

RUN docker-php-ext-install pgsql pdo_pgsql
