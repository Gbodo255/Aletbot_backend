FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Update Apache config (000-default.conf)
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Make scripts executable
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Port exposure
EXPOSE 80

# The entrypoint script will be handled by Render or via CMD
CMD ["/var/www/html/scripts/00-laravel-deploy.sh"]
