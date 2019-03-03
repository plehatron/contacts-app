FROM php:7.3-fpm-alpine

RUN apk add --no-cache \
    curl \
    git \
    bash \
    autoconf \
    build-base \
    libcurl \
    libzip-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        opcache \
        intl \
        zip

RUN pecl install \
    xdebug-beta

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY conf.d/* /usr/local/etc/php/conf.d/
COPY ./entrypoint.sh /entrypoint.sh

WORKDIR /srv/app

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]

EXPOSE 9000
