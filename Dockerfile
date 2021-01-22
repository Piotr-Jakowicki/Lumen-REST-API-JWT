FROM php:7.4-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install --quiet --yes --no-install-recommends \
    libzip-dev \ 
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_mysql gd

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN groupadd --gid 1000 appuser \
    && useradd --uid 1000 -g appuser \
    -G www-data,root --shell /bin/bash \
    --create-home appuser

USER appuser