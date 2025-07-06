FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Symfony CLI (опційно)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html

# Cache Composer
COPY composer.lock composer.json ./
RUN composer install --no-scripts --no-autoloader

# Copy project
COPY . .

# Optimized autoloader
RUN composer dump-autoload --optimize

CMD ["php-fpm"]