FROM php:8.3-apache

RUN apt update && apt -y install \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmagickwand-dev \
        git \
        zip \
        unzip \
        vim \
    && docker-php-ext-install opcache exif pdo_mysql bcmath -j$(nproc) gd \
    && docker-php-ext-enable exif \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
    && sed -i -e "s/;date.timezone =/date.timezone = Asia\/Tokyo/" /usr/local/etc/php/php.ini \
    && sed -i -e "s/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/" /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
