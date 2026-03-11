FROM php:8.4-fpm

# dependências do sistema e extensões PHP necessárias para Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev zlib1g-dev libicu-dev wget curl \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath intl zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* /var/www/html/

RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader || true

COPY . /var/www/html

RUN composer dump-autoload --optimize || true

RUN chown -R www-data:www-data /var/www/html \
  && mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000

CMD ["php-fpm"]