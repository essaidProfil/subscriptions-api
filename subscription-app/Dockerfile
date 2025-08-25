# --- Builder ---
FROM php:8.2-fpm-alpine AS builder
RUN apk add --no-cache git unzip libzip-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo_mysql mysqli opcache
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/subscriptions-api/subscription-app
COPY composer.json ./
RUN composer install --no-dev --no-interaction --no-ansi --no-scripts --prefer-dist || true
COPY . .
RUN composer install --no-dev --no-interaction --no-ansi --prefer-dist

# --- Runtime ---
FROM php:8.2-fpm-alpine AS app
RUN apk add --no-cache libzip
WORKDIR /var/www/subscriptions-api/subscription-app
COPY --from=builder /var/www/subscriptions-api/subscription-app /var/www/subscriptions-api/subscription-app
CMD ["php-fpm","-F"]
