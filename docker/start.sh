#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Pastikan public/storage adalah symlink valid ke storage/app/public.
# Kalau public/storage sudah ada sebagai folder biasa (bukan symlink,
# misal ikut ter-commit ke git), hapus dulu supaya storage:link tidak gagal.
if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then
    rm -rf public/storage
fi
php artisan storage:link

php-fpm -D
nginx -g "daemon off;"