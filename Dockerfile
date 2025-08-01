# Gunakan PHP 8.1 + extensions
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    mariadb-client \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur direktori kerja
WORKDIR /var/www

# Copy semua file project Laravel ke dalam container
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Beri permission ke Laravel
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

# Jalankan Laravel menggunakan PHP built-in server
CMD php artisan serve --host=0.0.0.0 --port=10000
