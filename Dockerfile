FROM php:8.3-fpm-bookworm

# Встановлення залежностей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    zlib1g-dev \
    libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Копіювання install-php-extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Встановлення PHP-розширень (без grpc, якщо воно вже є в образі)
RUN install-php-extensions xdebug protobuf pdo pdo_pgsql zip opcache redis intl

# Встановлення gRPC вручну, якщо потрібно
RUN pecl install grpc && docker-php-ext-enable grpc

# Копіювання Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Встановлення Symfony CLI (опціонально)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html

# Кешування Composer
COPY composer.lock composer.json ./
RUN composer install --no-scripts --no-autoloader --no-interaction

# Копіювання проєкту
COPY . .

# Оптимізація автозавантаження
RUN composer dump-autoload --optimize

CMD ["php-fpm"]

## --------------------------------- 1 ---------------------
#FROM php:8.3-fpm
#
#RUN apt-get update && apt-get install -y \
#    git \
#    curl \
#    zip unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
#    autoconf pkg-config make gcc g++ libc-dev libssl-dev protobuf-compiler
#
#RUN pecl install xdebug protobuf \
#    && docker-php-ext-enable xdebug protobuf \
#    && docker-php-ext-install pdo pdo_pgsql zip opcache
#
# RUN pecl install grpc \
#     && docker-php-ext-enable grpc
#
## --------------------------------- 1 ---------------------
#
## --------------------------------- 2 ---------------------
#
## System dependencies
##RUN apt-get update && apt-get install -y \
##    git curl zip unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
##    autoconf pkg-config make gcc g++ libc-dev \
##    # gRPC
##    libssl-dev \
##    protobuf-compiler \
##    #    libcurl4-openssl-dev libevent-dev \
##    #    && pecl install pecl_http \
##    #    && docker-php-ext-enable propro raphf http \
##    && pecl install xdebug grpc protobuf \
##    && docker-php-ext-enable xdebug grpc protobuf \
##    && docker-php-ext-install pdo pdo_pgsql zip opcache
#
## --------------------------------- 2 ---------------------
#
## Composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
## Symfony CLI (optional)
#RUN curl -sS https://get.symfony.com/cli/installer | bash \
#    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
#
#WORKDIR /var/www/html
#
## Cache Composer
#COPY composer.lock composer.json ./
#RUN composer install --no-scripts --no-autoloader
#
## Copy project
#COPY . .
#
## Optimized autoloader
#RUN composer dump-autoload --optimize
#
#CMD ["php-fpm"]