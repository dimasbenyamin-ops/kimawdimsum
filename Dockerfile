# ============================================================
# Kumaw Dimsum — Dockerfile
# PHP 8.3-FPM, multi-stage: base → development → production
# ============================================================

# --- Base Stage: Shared PHP extensions & system deps ---
FROM php:8.3-fpm-alpine AS base

LABEL maintainer="Kumaw Dimsum Team"
LABEL description="PHP 8.3 FPM for Laravel 11 + Reverb"

# System dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    supervisor

# PHP extensions required by Laravel 11
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp && \
    docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        exif \
        pcntl \
        bcmath \
        intl \
        opcache

# Redis extension (for Reverb broadcasting & cache)
RUN apk add --no-cache $PHPIZE_DEPS && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- Development Stage ---
FROM base AS development

ENV APP_ENV=local
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Node.js + npm (for Vite asset compilation in dev)
RUN apk add --no-cache nodejs npm

# Copy application source
COPY . .

# Install PHP dependencies (with dev deps)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node dependencies & build assets
RUN if [ -f "package.json" ]; then \
        npm install && npm run build; \
    fi

# Set correct permissions (mkdir -p guards in case dirs are missing)
RUN mkdir -p /var/www/html/storage/logs \
        /var/www/html/storage/framework/cache \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

# --- Production Stage ---
FROM base AS production

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . .

# Production Composer install (no dev deps)
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev

# Optimize Laravel for production
RUN php artisan route:cache \
    && php artisan view:cache

# Set correct permissions (mkdir -p guards in case dirs are missing)
RUN mkdir -p /var/www/html/storage/logs \
        /var/www/html/storage/framework/cache \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
