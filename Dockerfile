# ==============================================================================
# Dockerfile untuk Sistem Manajemen Nomor Surat (CodeIgniter 4)
# Siap untuk Deployment di Coolify / Docker Compose / Standalone Container
# ==============================================================================

FROM php:8.3-apache

# Set environment
ENV CI_ENVIRONMENT=production \
    APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies & PHP extensions required by CodeIgniter 4
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    git \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        gd \
        mysqli \
        pdo_mysql \
        mbstring \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy custom Apache & PHP configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Setup writable directories and permissions
RUN mkdir -p /var/www/html/writable/cache \
             /var/www/html/writable/logs \
             /var/www/html/writable/session \
             /var/www/html/writable/uploads/surat \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Copy entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=20s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f -L http://localhost/auth/login || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
