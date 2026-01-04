FROM php:8.2-fpm-alpine

# System deps
RUN apk add --no-cache \
    bash \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl zip

WORKDIR /var/www

# Copy EVERYTHING first (artisan must exist)
COPY . .

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
