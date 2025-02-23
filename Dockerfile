FROM wordpress:6.4-php8.1-apache

# Install dependencies for GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install additional PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy custom files (wp-content, etc)
COPY ./wp-content /var/www/html/wp-content/

# Set proper permissions with specific handling for themes
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html/wp-content/themes -type d -exec chmod 755 {} \; && \
    find /var/www/html/wp-content/themes -type f -exec chmod 644 {} \; && \
    chown -R www-data:www-data /var/www/html/wp-content/themes

# Expose port 80
EXPOSE 80