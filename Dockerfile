# Use official PHP Apache image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    unzip \
    curl \
    net-tools \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    opcache

# Configure PHP for WordPress
RUN { \
    echo 'display_errors = On'; \
    echo 'error_reporting = E_ALL'; \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'memory_limit = 256M'; \
    } > /usr/local/etc/php/conf.d/wordpress.ini

# Enable Apache modules
RUN a2enmod rewrite headers && \
    a2dismod -f autoindex

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Download and install WordPress
RUN curl -O https://wordpress.org/latest.tar.gz && \
    tar -xvf latest.tar.gz --strip-components=1 && \
    rm latest.tar.gz && \
    chown -R www-data:www-data . && \
    find . -type d -exec chmod 755 {} \; && \
    find . -type f -exec chmod 644 {} \;

# Create wp-config.php from environment variables
COPY wp-config-render.php /var/www/html/wp-config.php

# Configure Apache for proper port binding
RUN sed -i 's/Listen 80/Listen 0.0.0.0:${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's/ServerName localhost/ServerName 0.0.0.0/g' /etc/apache2/apache2.conf

# Create debug script
RUN echo '#!/bin/bash\n\
echo "Debugging container startup..."\n\
echo "PORT environment variable: $PORT"\n\
echo "Checking Apache ports:"\n\
grep Listen /etc/apache2/ports.conf\n\
echo "Checking VirtualHost configuration:"\n\
grep VirtualHost /etc/apache2/sites-available/000-default.conf\n\
echo "Listing active ports:"\n\
netstat -tlpn\n\
echo "Listing /var/www/html contents:"\n\
ls -la /var/www/html\n\
echo "Starting Apache..."\n\
apache2-foreground' > /usr/local/bin/start-debug.sh && \
chmod +x /usr/local/bin/start-debug.sh

# Create .htaccess
RUN echo 'RewriteEngine On\n\
RewriteBase /\n\
RewriteRule ^index\.php$ - [L]\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule . /index.php [L]' > /var/www/html/.htaccess

EXPOSE ${PORT}

# Use debug script as entrypoint
CMD ["/usr/local/bin/start-debug.sh"]
