FROM php:7.3-fpm-alpine

ARG build_env=dev

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
    libpng-dev \
    su-exec

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        opcache \
        intl \
        zip

COPY --from=composer:1.8 /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY conf.d/* /usr/local/etc/php/conf.d/
COPY ./config.sh /config.sh
COPY ./entrypoint.sh /entrypoint.sh

RUN /config.sh

WORKDIR /srv/app

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]

EXPOSE 9000
