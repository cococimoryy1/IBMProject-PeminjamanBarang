# Gunakan image PHP sebagai basis
FROM php:8.1-fpm

# Instal dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Instal ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin kode aplikasi
COPY . /var/www/html
RUN mkdir -p /var/log/nginx && mkdir -p /var/cache/nginx

# Instal dependensi PHP
RUN composer install --optimize-autoloader --no-dev

# Instal dependensi frontend (jika ada)
RUN if [ -f package.json ]; then npm install --production && npm run build; fi

# Konfigurasi Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Set working directory
WORKDIR /var/www/html

# Expose port
EXPOSE 8080

# Jalankan aplikasi
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
