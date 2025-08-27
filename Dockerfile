# Gunakan PHP 8.2 dengan Composer
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy semua file project
COPY . .

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Install dependency frontend (Vite) & build
RUN npm install && npm run build

# Cache Laravel config
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Expose port
EXPOSE 10000

# Jalankan Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
