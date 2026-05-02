FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    poppler-utils \
    $PHPIZE_DEPS

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    mbstring \
    opcache

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /app

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app
RUN mkdir -p /app/var/cache /app/var/log \
    && chown -R app:app /app \
    && chmod -R 775 /app/var

USER app