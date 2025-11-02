#!/bin/bash
set -e

echo "Deployment started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
# (php artisan down) || true

# Resolve safe directory issues
echo "Configuring Git safe directory..."
git config --global --add safe.directory $(pwd)

# Remove extraheader configuration to avoid issues with GitHub Actions
echo "Removing extraheader configuration..."
git config --local --unset-all http.https://github.com/.extraheader || true

# Pull the latest version of the app
echo "Pulling the latest changes..."
git pull --no-edit

# Install composer dependencies
echo "Installing composer dependencies..."
echo "y" | composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Clear the old cache
echo "Clearing old cache..."
php artisan clear-compiled
php artisan cache:clear
php artisan config:clear
php artisan event:clear
php artisan optimize:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "Running database migrations..."
echo "y" | php artisan migrate

# Install Node Modules
echo "Installing Node modules..."
npm install

# Build assets
echo "Building assets..."
npm run build

# Recreate cache
echo "Recreating cache..."
php artisan config:cache
php artisan event:cache
php artisan optimize
php artisan route:cache
php artisan view:cache

# Exit maintenance mode
# echo "Exiting maintenance mode..."
# php artisan up

echo "Deployment finished!"
