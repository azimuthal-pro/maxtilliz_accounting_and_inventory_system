FROM php:8.2-apache

# Fix MPM conflict - must disable event before enabling prefork
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    a2enmod mpm_prefork && \
    a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project into Apache web root
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to use Railway's dynamic $PORT
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]