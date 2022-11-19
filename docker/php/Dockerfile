FROM php:8.1.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get update; \
    # Imagick extension
    apt-get install -y libmagickwand-dev; \
    pecl install imagick; \
    docker-php-ext-enable imagick; \
    # Success
    true

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set TimeZone
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN sed -ri -e 's!;date.timezone =!date.timezone = "America/Sao_Paulo"!g' /usr/local/etc/php/php.ini
