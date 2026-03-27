FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-install \
        bcmath \
        dom \
        mbstring \
        pdo_pgsql \
        xml \
        zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
