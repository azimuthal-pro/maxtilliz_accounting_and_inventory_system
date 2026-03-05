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

# Overwrite the entire nginx.conf cleanly
RUN cat > /etc/nginx/nginx.conf << 'NGINXEOF'
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 768;
}

http {
    sendfile on;
    tcp_nopush on;
    types_hash_max_size 2048;
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

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
        }

        location ~ /\.ht {
            deny all;
        }
    }
}
NGINXEOF

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
