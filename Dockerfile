FROM wordpress:latest

# Install required packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy WordPress config
COPY wp-config-render.php /var/www/html/wp-config.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Configure PHP
RUN { \
    echo 'memory_limit = 256M'; \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'max_execution_time = 300'; \
    echo 'max_input_vars = 3000'; \
} > /usr/local/etc/php/conf.d/wordpress.ini

WORKDIR /var/www/html

# Add an entrypoint script that waits for MySQL
RUN echo '#!/bin/bash\n\
while ! nc -z mysql-db 3306; do\n\
  echo "Waiting for MySQL to be ready..."\n\
  sleep 2\n\
done\n\
echo "MySQL is ready!"\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint-wrapper.sh && \
    chmod +x /usr/local/bin/docker-entrypoint-wrapper.sh

CMD ["/usr/local/bin/docker-entrypoint-wrapper.sh"]