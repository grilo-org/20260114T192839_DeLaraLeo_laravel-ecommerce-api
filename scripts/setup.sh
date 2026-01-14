#!/bin/bash

# Laravel Microservices Boilerplate - Setup Script
# This script automates the initial setup of the Laravel application

set -e

echo "🚀 Starting Laravel Microservices Boilerplate Setup..."
echo ""

# Check if .env exists, if not copy from .env.example
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "ℹ️  .env file already exists, skipping..."
fi
echo ""

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "✅ Composer dependencies installed"
echo ""

# Generate application key if not set
if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    echo "✅ Application key generated"
else
    echo "ℹ️  Application key already set, skipping..."
fi
echo ""

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force
echo "✅ Database migrations completed"
echo ""

# Clear and cache config
echo "🧹 Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Configuration cleared"
echo ""

echo "✨ Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "   1. Review and update .env file with your configuration"
echo "   2. For local testing: php artisan serve (optional)"
echo "   3. For production: Use Docker with PHP-FPM (see microservices-infra project)"
echo "   4. Visit http://localhost:8000/api/health to test the health endpoint"
echo ""

