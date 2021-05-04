FROM composer:2.0.13
FROM php:8.0.0-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache \
        ca-certificates \
        icu-libs \
        git \
        unzip \
        libzip-dev \
        zlib-dev \
        libxml2-dev && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev  \
        dos2unix && \
    docker-php-ext-install \
        intl \
        zip \
        xml \
        soap && \
    pecl install apcu && \
    docker-php-ext-enable apcu && \
    docker-php-ext-enable opcache && \
    docker-php-ext-install bcmath && \
    docker-php-ext-configure intl && \
    echo "short_open_tag = off" >> /usr/local/etc/php/php.ini && \
    echo "memory_limit = -1" >> /usr/local/etc/php/php.ini && \
    apk del .build-deps && \
    apk add gosu --update --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing/ --allow-untrusted && \
    addgroup bar && \
    adduser -D -h /home -s /bin/sh -G bar foo

ADD entrypoint.sh /entrypoint
RUN chmod +x /entrypoint

# Convert to unix the script
RUN echo http://nl.alpinelinux.org/alpine/edge/testing >> /etc/apk/repositories && \
    echo http://nl.alpinelinux.org/alpine/edge/community >> /etc/apk/repositories && \
    apk add --no-cache --virtual .build-deps dos2unix  && \
    dos2unix /entrypoint  && \
    apk del .build-deps  && \
    rm -rf /var/cache/apk/*

ENTRYPOINT ["/entrypoint"]
