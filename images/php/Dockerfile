FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libmcrypt-dev \
    libxslt-dev \
    && pecl install mcrypt-1.0.3 \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install xsl \
    && docker-php-ext-install bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ADD php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/app.test

CMD ["php-fpm"]