# FROM php 8.2-fpm

# RUN apt-get update && apt-get install -y \
#     git \
#     unzip \
#     libzip-dev \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     curl

# RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www/api

# COPY . .

# RUN composer install --no-dev --optimize-autoloader

# RUN chown -R www-data:www-data /var/www/api/storage /var/www/api/bootstrap/cache

# EXPOSE 9000

# CMD ["php-fpm"]

# Base PHP image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
