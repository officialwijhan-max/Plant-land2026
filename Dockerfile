# syntax=docker/dockerfile:1

# ==============================
# Stage 1: Install Composer deps
# ==============================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs \
    --no-autoloader


# ==============================
# Stage 2: PHP + Apache
# ==============================
FROM php:8.2-apache


# ==============================
# System dependencies
# ==============================
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libicu-dev \
        unzip \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        zip \
        exif \
        pcntl \
    && a2enmod rewrite headers alias \
    && rm -rf /var/lib/apt/lists/*


# ==============================
# Apache Configuration
# ==============================

# IMPORTANT:
# This project uses project root as Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html

RUN { \
        echo '<Directory /var/www/html>'; \
        echo '    Options FollowSymLinks'; \
        echo '    AllowOverride All'; \
        echo '    Require all granted'; \
        echo '</Directory>'; \
        echo ''; \
        echo '# Serve backend static assets from Laravel public directory'; \
        echo 'Alias /backEnd/ /var/www/html/public/backEnd/'; \
        echo ''; \
        echo '<Directory /var/www/html/public/backEnd>'; \
        echo '    Options FollowSymLinks'; \
        echo '    AllowOverride None'; \
        echo '    Require all granted'; \
        echo '</Directory>'; \
    } > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel


# Avoid Apache ServerName warning
RUN echo 'ServerName localhost' \
    > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername


# ==============================
# PHP Configuration
# ==============================
RUN { \
        echo 'memory_limit=512M'; \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
        echo 'max_execution_time=300'; \
    } > /usr/local/etc/php/conf.d/app.ini


# ==============================
# Application
# ==============================
COPY --from=vendor /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor

COPY . .


# ==============================
# Composer Autoload
# ==============================
RUN composer dump-autoload \
        --no-dev \
        --optimize \
        --no-interaction \
    && php artisan package:discover --ansi || true


# ==============================
# Permissions
# ==============================
RUN mkdir -p \
        storage \
        bootstrap/cache \
        public/uploads \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
        public/uploads \
    && chmod -R 775 \
        storage \
        bootstrap/cache \
        public/uploads


# ==============================
# Entrypoint
# ==============================
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh


# ==============================
# Apache Port
# ==============================
EXPOSE 80


# ==============================
# Start Application
# ==============================
ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]
