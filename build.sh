#!/bin/bash
set -e

echo "Installing backend dependencies..."
cd backend
composer install --no-dev --optimize-autoloader
cd ..

echo "Installing frontend dependencies..."
cd frontend
npm ci --production=false
echo "Building frontend..."
npm run build
cd ..

echo "Build completed successfully!"
