FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project into Apache web root
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to use Railway's dynamic $PORT
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-enabled/000-default.conf

EXPOSE ${PORT}

CMD ["apache2-foreground"]