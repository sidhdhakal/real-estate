FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy source code to Apache's web root
COPY . /var/www/html/

# Set file permissions (optional)
RUN chown -R www-data:www-data /var/www/html/

# Expose Apache port
EXPOSE 80
