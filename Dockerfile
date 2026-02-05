FROM php:5.6-fpm

# Update apt sources to use archived Debian repositories
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    sed -i '/stretch-updates/d' /etc/apt/sources.list

# Install dependencies
RUN apt-get update && apt-get install -y --allow-unauthenticated \
    libpq-dev \
    nginx \
    git \
    unzip \
    procps \
    net-tools \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy PHP configuration
COPY docker/php/timezone.ini /usr/local/etc/php/conf.d/timezone.ini

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Expose port 80 for nginx
EXPOSE 80

# Start both PHP-FPM and nginx
CMD ["/start.sh"]
