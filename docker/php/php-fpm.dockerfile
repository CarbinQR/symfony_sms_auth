FROM php:8.2.0-fpm-alpine

RUN apk update && apk add --no-cache \
        build-base \
        zlib-dev \
        autoconf \
        bash \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
        rabbitmq-c-dev \
        oniguruma-dev \
        unzip \
        mc \
        make \
    curl-dev \
    linux-headers \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install amqp \
    && docker-php-ext-install -j$(nproc) \
    mbstring \
    sockets \
    curl \
    zip \
    mysqli \
    pdo_mysql \
    pdo \
    && docker-php-ext-enable amqp\
    && apk del .build-deps

RUN set -eux; \
    apk --update --no-cache add \
        postgresql-dev \
        libintl \
        libmemcached-libs \
        zlib \
        libmemcached-dev \
        autoconf \
        g++ \
    && docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    && pecl install igbinary \
    && pecl install memcached \
    && apk del autoconf g++ make libmemcached-dev

ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer