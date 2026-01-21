# FROM php:8.2-fpm-alpine

# # System dependencies
# RUN apk add --no-cache \
#     bash \
#     icu-dev \
#     oniguruma-dev \
#     libzip-dev \
#     zip \
#     unzip

# # PHP extensions (IMPORTANT: intl)
# RUN docker-php-ext-install pdo pdo_mysql intl zip

# WORKDIR /var/www

# # Copy full project (artisan MUST exist)
# COPY . .

# # Install Composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # Install dependencies
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# # Permissions
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 775 storage bootstrap/cache

# EXPOSE 9000
# CMD ["php-fpm"]

# Below file for kuberneties setup

FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    nginx \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql intl zip

WORKDIR /var/www
COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]


