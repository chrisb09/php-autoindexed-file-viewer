# Multi-stage build for PHP File Browser
FROM php:8.2-fpm-bookworm AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    mediainfo \
    ffmpeg \
    poppler-utils \
    imagemagick \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libsqlite3-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure GD with support for JPEG and freetype
RUN docker-php-ext-configure gd --with-jpeg --with-freetype

# Install PHP extensions
RUN docker-php-ext-install \
    gd \
    mbstring \
    zip \
    pdo_sqlite \
    xml

# Create cache directory
RUN mkdir -p /app/.cache_fb && chown -R www-data:www-data /app/.cache_fb

# Set working directory
WORKDIR /app

# Copy application files
#COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data index.php ./

# Ensure files directory exists
RUN mkdir -p /app/files && chown -R www-data:www-data /app/files

# Switch to www-data user
USER www-data

EXPOSE 9000

CMD ["php-fpm"]
