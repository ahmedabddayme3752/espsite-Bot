FROM wordpress:6.4-apache

# Install required PHP extensions and utilities
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    postgresql-client \
    libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql pgsql

# Create PHP configuration directories
RUN mkdir -p /usr/local/etc/php/conf.d

# Configure PHP
COPY <<EOF /usr/local/etc/php/conf.d/uploads.ini
upload_max_filesize = 64M
post_max_size = 64M
EOF

COPY <<EOF /usr/local/etc/php/conf.d/error-logging.ini
log_errors = On
error_log = /var/www/html/php-errors.log
EOF

# Enable Apache modules
RUN a2enmod rewrite headers

# Create WordPress directory
WORKDIR /var/www/html

# Copy WordPress files
COPY . .

# Create .htaccess
RUN echo "# BEGIN WordPress" > .htaccess && \
    echo "<IfModule mod_rewrite.c>" >> .htaccess && \
    echo "RewriteEngine On" >> .htaccess && \
    echo "RewriteBase /" >> .htaccess && \
    echo "RewriteRule ^index\.php$ - [L]" >> .htaccess && \
    echo "RewriteCond %{REQUEST_FILENAME} !-f" >> .htaccess && \
    echo "RewriteCond %{REQUEST_FILENAME} !-d" >> .htaccess && \
    echo "RewriteRule . /index.php [L]" >> .htaccess && \
    echo "</IfModule>" >> .htaccess && \
    echo "# END WordPress" >> .htaccess

# Copy wp-config.php
COPY wp-config-render.php wp-config.php

# Create start script
RUN echo '#!/bin/bash\n\
\n\
echo "Current DATABASE_URL: $DATABASE_URL"\n\
\n\
# Wait for the database to be ready\n\
echo "Testing database connection..."\n\
max_retries=30\n\
counter=0\n\
\n\
# Extract host from DATABASE_URL\n\
DB_HOST=$(echo $DATABASE_URL | grep -o "@.*/" | sed "s/@//" | sed "s/\\///g")\n\
echo "Extracted DB_HOST: $DB_HOST"\n\
\n\
while ! PGPASSWORD=$WORDPRESS_DB_PASSWORD psql -h "$DB_HOST" -U "$WORDPRESS_DB_USER" -d "$WORDPRESS_DB_NAME" -c "\\q" 2>/dev/null; do\n\
    counter=$((counter+1))\n\
    if [ $counter -gt $max_retries ]; then\n\
        echo "Failed to connect to database after $max_retries attempts"\n\
        echo "DB_HOST: $DB_HOST"\n\
        echo "DB_USER: $WORDPRESS_DB_USER"\n\
        echo "DB_NAME: $WORDPRESS_DB_NAME"\n\
        exit 1\n\
    fi\n\
    echo "Waiting for database connection... ($counter/$max_retries)"\n\
    sleep 2\n\
done\n\
\n\
echo "Database connection successful"\n\
\n\
# Configure Apache port\n\
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    touch /var/www/html/php-errors.log && \
    chown www-data:www-data /var/www/html/php-errors.log

EXPOSE ${PORT}

CMD ["/usr/local/bin/start.sh"]
