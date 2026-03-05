FROM php:8.2-fpm-bullseye

# Install nginx and supervisor
RUN apt-get update && \
    apt-get install -y nginx supervisor && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project files
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Remove default nginx config
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/sites-available/default

# Write nginx config - listens on 8080 (Railway default fallback)
RUN cat > /etc/nginx/conf.d/app.conf << 'NGINXEOF'
server {
    listen 8080 default_server;
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
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINXEOF

# Remove the events/http wrapper issue - fix nginx.conf to not conflict
RUN sed -i 's/include \/etc\/nginx\/sites-enabled\/\*;//' /etc/nginx/nginx.conf && \
    echo "include /etc/nginx/conf.d/*.conf;" >> /etc/nginx/nginx.conf || true

# Supervisor config
RUN cat > /etc/supervisor/conf.d/app.conf << 'SUPEOF'
[supervisord]
nodaemon=true
user=root
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

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
