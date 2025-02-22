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

# Create start script
RUN echo '#!/bin/bash\n\
\n\
# Debug information\n\
echo "Starting container with the following configuration:"\n\
echo "PORT: $PORT"\n\
echo "WORDPRESS_DB_HOST: $WORDPRESS_DB_HOST"\n\
echo "WORDPRESS_DB_NAME: $WORDPRESS_DB_NAME"\n\
echo "WORDPRESS_DB_USER: $WORDPRESS_DB_USER"\n\
\n\
# Configure Apache port\n\
if [ -n "$PORT" ]; then\n\
    echo "Setting Apache port to: $PORT"\n\
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf\n\
fi\n\
\n\
# Create wp-config.php\n\
cat > wp-config.php << EOL\n\
<?php\n\
// Database configuration\n\
define( "DB_NAME", getenv("WORDPRESS_DB_NAME") );\n\
define( "DB_USER", getenv("WORDPRESS_DB_USER") );\n\
define( "DB_PASSWORD", getenv("WORDPRESS_DB_PASSWORD") );\n\
define( "DB_HOST", getenv("WORDPRESS_DB_HOST") . ":5432?sslmode=verify-full&sslrootcert=/etc/ssl/certs/ca-certificates.crt" );\n\
define( "DB_CHARSET", "utf8" );\n\
define( "DB_COLLATE", "" );\n\
\n\
// Authentication Unique Keys and Salts\n\
define("AUTH_KEY",         "$(openssl rand -base64 48)");\n\
define("SECURE_AUTH_KEY",  "$(openssl rand -base64 48)");\n\
define("LOGGED_IN_KEY",    "$(openssl rand -base64 48)");\n\
define("NONCE_KEY",        "$(openssl rand -base64 48)");\n\
define("AUTH_SALT",        "$(openssl rand -base64 48)");\n\
define("SECURE_AUTH_SALT", "$(openssl rand -base64 48)");\n\
define("LOGGED_IN_SALT",   "$(openssl rand -base64 48)");\n\
define("NONCE_SALT",       "$(openssl rand -base64 48)");\n\
\n\
\$table_prefix = "wp_";\n\
\n\
define( "WP_DEBUG", true );\n\
\n\
// Additional WordPress Configuration\n\
if (getenv("WORDPRESS_CONFIG_EXTRA")) {\n\
    eval(getenv("WORDPRESS_CONFIG_EXTRA"));\n\
}\n\
\n\
if ( ! defined( "ABSPATH" ) ) {\n\
    define( "ABSPATH", __DIR__ . "/" );\n\
}\n\
\n\
require_once ABSPATH . "wp-settings.php";\n\
EOL\n\
\n\
echo "Database configuration:"\n\
echo "DB_HOST: $WORDPRESS_DB_HOST"\n\
echo "DB_USER: $WORDPRESS_DB_USER"\n\
echo "DB_NAME: $WORDPRESS_DB_NAME"\n\
\n\
# Test database connection\n\
max_retries=30\n\
counter=0\n\
\n\
until PGPASSWORD=$WORDPRESS_DB_PASSWORD psql -h "$WORDPRESS_DB_HOST" -p "$WORDPRESS_DB_PORT" -U "$WORDPRESS_DB_USER" -d "$WORDPRESS_DB_NAME" -c "\\q" 2>/dev/null; do\n\
    counter=$((counter+1))\n\
    if [ $counter -gt $max_retries ]; then\n\
        echo "Failed to connect to database after $max_retries attempts"\n\
        echo "DB_HOST: $WORDPRESS_DB_HOST"\n\
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
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    touch /var/www/html/php-errors.log && \
    chown www-data:www-data /var/www/html/php-errors.log

# Configure Apache for dynamic port
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Add Apache directory index configuration
RUN echo 'DirectoryIndex index.php index.html' >> /etc/apache2/mods-available/dir.conf

EXPOSE ${PORT}

CMD ["/usr/local/bin/start.sh"]
