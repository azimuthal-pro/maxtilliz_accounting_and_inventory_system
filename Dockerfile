FROM php:8.2-apache

# Ensure only ONE MPM is enabled (prefork for mod_php)
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# Install common PHP extensions (adjust if you need more)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project into Apache web root
COPY . /var/www/html/

# Permissions (safe default)
RUN chown -R www-data:www-data /var/www/html

# Railway uses $PORT; Apache listens on 80 inside the container, Railway maps it automatically.
EXPOSE 80
