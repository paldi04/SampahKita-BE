FROM php:8.2-fpm

# Install system dependencies and PHP extensions as needed
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libmcrypt-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /app

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Expose port 8081 and start PHP-FPM server
EXPOSE 8081
CMD ["php-fpm"]
