# Laravel Dockerfile for Railway deployment
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    bash \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Copy package.json files
COPY package*.json ./

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize --no-dev

# Install Node dependencies
RUN npm ci --prefer-offline --no-audit --production \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules/.cache

# Create necessary directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /run/nginx \
    /var/log/supervisor

# Set permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /run/nginx \
    /var/log/nginx \
    /var/log/supervisor \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 777 storage/framework/sessions

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Add PHP memory limit configuration
RUN echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "opcache.memory_consumption = 32" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "max_execution_time = 30" >> /usr/local/etc/php/conf.d/performance.ini

# Expose port
EXPOSE 8000

# Start command
CMD ["/start.sh"]
