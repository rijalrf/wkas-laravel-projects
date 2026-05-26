FROM php:8.4-cli-alpine

# Install system dependencies & PHP extensions required for Laravel, PDF/Excel generation
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libzip-dev \
    oniguruma-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo_mysql mbstring bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy files
COPY . .

# Set permissions for Laravel
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8090

CMD php artisan serve --host=0.0.0.0 --port=8090
