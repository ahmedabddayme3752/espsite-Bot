# Use official PHP Apache image
FROM php:8.2-apache

# Install system dependencies and dev packages
RUN apt-get update && apt-get install -y \
    ca-certificates \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Configure GD extension
RUN docker-php-ext-configure gd --with-jpeg --with-webp

# Install PHP extensions
RUN docker-php-ext-install -j "$(nproc)" \
    gd \
    zip \
    pdo_mysql \
    mysqli \
    opcache

# Install MySQL client
RUN apt-get update && \
    apt-get install -y --no-install-recommends default-mysql-client && \
    rm -rf /var/lib/apt/lists/*

# Configure PHP for WordPress
RUN { \
    echo 'display_errors = On'; \
    echo 'error_reporting = E_ALL'; \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 120'; \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    } > /usr/local/etc/php/conf.d/wordpress.ini

# Configure Apache
RUN a2enmod rewrite headers ssl
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN { \
    echo '<VirtualHost *:${PORT}>'; \
    echo '    DocumentRoot /var/www/html'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '    <Directory /var/www/html/>'; \
    echo '        Options FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf

# Download and extract WordPress
RUN curl -o wordpress.tar.gz -fL https://wordpress.org/latest.tar.gz \
    && tar -xzf wordpress.tar.gz --strip-components=1 -C /var/www/html \
    && rm wordpress.tar.gz

# Create wp-content/uploads directory and set permissions
RUN mkdir -p /var/www/html/wp-content/uploads

# Create .htaccess file
RUN echo 'RewriteEngine On\n\
RewriteBase /\n\
RewriteRule ^index\.php$ - [L]\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule . /index.php [L]' > /var/www/html/.htaccess

# Create wait-for-mysql script
RUN echo '#!/bin/bash\n\
echo "Waiting for MySQL..."\n\
while ! nc -z $DB_HOST $DB_PORT; do\n\
  sleep 1\n\
done\n\
echo "MySQL is up - starting Apache"\n\
apache2-foreground' > /usr/local/bin/wait-for-mysql.sh && \
    chmod +x /usr/local/bin/wait-for-mysql.sh

# Create start script with port configuration
RUN echo '#!/bin/bash\n\
PORT="${PORT:-10000}"\n\
echo "Listen ${PORT}" > /etc/apache2/ports.conf\n\
sed -i "s/\${PORT}/$PORT/" /etc/apache2/sites-available/000-default.conf\n\
/usr/local/bin/wait-for-mysql.sh' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Copy WordPress config
COPY wp-config-render.php /var/www/html/wp-config.php

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Healthcheck to verify database connection
HEALTHCHECK --interval=30s --timeout=10s --retries=3 \
    CMD mysql -h "$MYSQL_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT 1;" || exit 1

# Set working directory
WORKDIR /var/www/html

# Set the default command
CMD ["/usr/local/bin/start.sh"]
