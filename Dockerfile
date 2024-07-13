# Use the official PHP image with Apache
FROM php:8.3-apache

# Install necessary extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set environment variable to allow Composer plugins to run
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set the working directory
WORKDIR /var/www/html

# Copy the PHP application files to the working directory
COPY . /var/www/html/

# Expose port 8000
EXPOSE 8000
