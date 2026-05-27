# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Install Composer dependencies
FROM composer:2.8 AS composer-builder
WORKDIR /app
COPY composer*.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Stage 3: Final production image
FROM php:8.4-cli-alpine

# Install system dependencies & PHP extensions required for Laravel
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev

RUN docker-php-ext-install pdo_pgsql mbstring bcmath gd zip

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Copy vendor dependencies from Composer stage
COPY --from=composer-builder /app/vendor ./vendor

# Copy compiled Vite assets from Frontend stage
COPY --from=frontend-builder /app/public/build ./public/build

# Set permissions for Laravel storage & cache
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www

EXPOSE 8090

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8090"]

