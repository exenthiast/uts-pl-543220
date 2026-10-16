# ==============================================================================
# Dockerfile Backend Laravel 13 dengan Server Aplikasi FrankenPHP (Bonus +10)
# Base image versi eksplisit (bukan latest) berbasis Alpine yang ringan
# ==============================================================================
FROM dunglas/frankenphp:1.4-php8.4-alpine

# Pasang ekstensi PHP yang dibutuhkan untuk MySQL, PostgreSQL, dan performa Laravel
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pcntl \
    opcache \
    zip \
    intl

# Salin binary Composer resmi versi stabil (bukan latest)
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Atur direktori kerja aplikasi
WORKDIR /app

# OPTIMASI LAYER CACHING DOCKER (Soal 5a):
# Salin berkas manifes dependensi composer terlebih dahulu sebelum source code
COPY composer.json composer.lock ./

# Pasang dependensi production tanpa autoloader (agar lapisan di-cache)
RUN composer install \
    --no-dev \
    --no-autoloader \
    --no-scripts \
    --no-interaction \
    --prefer-dist

# Salin seluruh kode aplikasi (tanpa file yang diabaikan di .dockerignore)
COPY . .

# Generate autoloader yang teroptimasi untuk production
RUN composer dump-autoload --optimize --no-dev

# Atur perizinan direktori storage dan cache Laravel
RUN mkdir -p storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Port listener aplikasi
EXPOSE 8000

# Server FrankenPHP menggantikan php artisan serve / PHP-FPM + Nginx (Soal 7)
CMD ["frankenphp", "php-server", "--listen", ":8000", "--root", "public/", "--access-log"]
