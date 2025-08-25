# --- builder ---
FROM php:8.2-fpm-alpine AS builder
RUN apk add --no-cache git zip unzip libzip-dev oniguruma-dev icu-dev \
    && docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/subscriptions-api/subscription-app

COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php
RUN composer install --no-interaction --prefer-dist --no-scripts || true

COPY . .
# En prod, tu peux passer en --no-dev + optimize
# RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --- runtime ---
FROM php:8.2-fpm-alpine AS runtime
RUN apk add --no-cache libzip \
    && docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/subscriptions-api/subscription-app
COPY --from=builder /var/www/subscriptions-api/subscription-app /var/www/subscriptions-api/subscription-app
CMD ["php-fpm","-F"]
