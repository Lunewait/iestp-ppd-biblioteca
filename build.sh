#!/bin/bash

# Install PHP extensions
# composer install is already done by Render

# Clear config cache
php artisan config:clear

# Generate app key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

echo "Build completed successfully!"
