FROM php:8.3-fpm

# Install composer
RUN echo "\e[1;33mInstall COMPOSER\e[0m"
RUN cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update

# Install useful tools
RUN apt-get -y install apt-utils nano wget dialog vim

# Update and install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m"
RUN apt-get -qq update && apt-get -qq -y install --fix-missing \
    apt-utils \
    build-essential \
    git \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libzip-dev \
    zip \
    wget \
    unzip \
    libbz2-dev \
    locales \
    libmcrypt-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev

# install latest version of composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
