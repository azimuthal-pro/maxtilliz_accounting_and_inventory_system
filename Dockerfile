FROM php:8.2-fpm-bullseye

# Install nginx and supervisor (supervisor manages both nginx + php-fpm)
RUN apt-get update && \
    apt-get install -y nginx supervisor && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Write nginx config
RUN cat > /etc/nginx/sites-available/default << 'NGINXEOF'
server {
    listen __PORT__;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
NGINXEOF

# Write supervisor config to manage both processes
RUN cat > /etc/supervisor/conf.d/app.conf << 'SUPEOF'
[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0

[program:php-fpm]
command=php-fpm --nodaemonize
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
SUPEOF

# Startup script: inject PORT then launch supervisor
RUN printf '#!/bin/sh\nPORT=${PORT:-80}\nsed -i "s/__PORT__/$PORT/" /etc/nginx/sites-available/default\nexec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf\n' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
