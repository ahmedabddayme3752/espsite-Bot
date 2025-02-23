# Use official PHP Apache image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    ca-certificates \
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
    opcache \
    mysqli

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
RUN echo "ServerName 0.0.0.0" >> /etc/apache2/apache2.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Create start script with proper port binding
RUN echo '#!/bin/bash\n\
PORT="${PORT:-10000}"\n\
echo "Listen 0.0.0.0:$PORT" > /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
sed -i "s/VirtualHost \*:80/VirtualHost *:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
echo "Starting Apache on 0.0.0.0:$PORT"\n\
apache2-foreground' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Configure WordPress database adapter
RUN mkdir -p /var/www/html/wp-content/plugins/pg4wp && \
    ln -sf /var/www/html/wp-content/plugins/pg4wp/db.php /var/www/html/wp-content/db.php && \
    chown -R www-data:www-data /var/www/html

# Set environment variables
ENV PORT=10000
ENV APACHE_PORT=10000

# Set working directory
WORKDIR /var/www/html

# Expose port 10000 (Render's default)
EXPOSE 10000

# Download and install WordPress
RUN curl -O https://wordpress.org/latest.tar.gz && \
    tar -xvf latest.tar.gz --strip-components=1 && \
    rm latest.tar.gz && \
    # Create pg4wp directory and download
    mkdir -p wp-content/plugins/pg4wp && \
    curl -o wp-content/plugins/pg4wp/db.php https://raw.githubusercontent.com/PostgreSQL-For-Wordpress/postgresql-for-wordpress/master/pg4wp/db.php && \
    # Set permissions
    chown -R www-data:www-data . && \
    chmod -R 755 wp-content/plugins/pg4wp && \
    find . -type d -exec chmod 755 {} \; && \
    find . -type f -exec chmod 644 {} \;

# Create wp-config.php from environment variables
COPY wp-config-render.php /var/www/html/wp-config.php

# Create .htaccess
RUN echo 'RewriteEngine On\n\
RewriteBase /\n\
RewriteRule ^index\.php$ - [L]\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule . /index.php [L]' > /var/www/html/.htaccess

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql \
    && update-ca-certificates

# Set the default command
CMD ["/usr/local/bin/start.sh"]
