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
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd zip

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

# Configure Apache for dynamic port
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Create debug script
RUN echo '#!/bin/bash\n\
echo "Debugging container startup..."\n\
echo "Listing /var/www/html contents:"\n\
ls -la /var/www/html\n\
echo "Apache configuration:"\n\
apache2ctl -S\n\
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
