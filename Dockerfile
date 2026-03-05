FROM php:8.2-fpm-bullseye

# Install nginx
RUN apt-get update && \
    apt-get install -y nginx && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed by the app
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy all project files into web root
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Write nginx config with placeholder for PORT
RUN cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen PORT_PLACEHOLDER;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Create php-fpm socket directory
RUN mkdir -p /var/run/php

# Write startup script that substitutes real PORT at runtime
RUN printf '#!/bin/sh\nACTUAL_PORT=${PORT:-80}\nsed -i "s/PORT_PLACEHOLDER/$ACTUAL_PORT/" /etc/nginx/sites-available/default\nphp-fpm -D\nnginx -g "daemon off;"\n' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
