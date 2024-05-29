# Use the official PHP 8.2 image as the base image
FROM php:8.2

# Set the working directory inside the container
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Copy the application files to the container
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install application dependencies
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist

# Expose port 8081
EXPOSE 8081

# Start the PHP development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8081"]