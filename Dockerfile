FROM wordpress:6.4-apache

# Install required packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    default-mysql-client \
    netcat-openbsd && \
    rm -rf /var/lib/apt/lists/*

# Copy WordPress config
COPY wp-config-render.php /var/www/html/wp-config.php

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

WORKDIR /var/www/html