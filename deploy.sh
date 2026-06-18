#!/usr/bin/env bash
# Deploy/update script for mess-jelita (native Laravel on biznet_gio, no Docker).
# Usage on server: cd ~/apps/kas-mess-v2 && ./deploy.sh
set -euo pipefail
cd "$(dirname "$0")"

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"

echo "==> git pull (main)"
git pull --ff-only origin main

echo "==> composer install (no-dev, optimized)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> build frontend assets (npm)"
npm ci --no-audit --no-fund
npm run build

echo "==> migrate"
php artisan migrate --force

echo "==> cache (config/route/view/event)"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> permissions (www-data)"
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

echo "==> reload php-fpm (clear opcache)"
sudo systemctl reload php8.4-fpm || true

echo "==> deploy done: https://mess-jelita.hnifkumara.com"
