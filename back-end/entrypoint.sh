#/bin/bash
echo "Composer install"
composer install --prefer-dist --no-dev --no-scripts --no-suggest

echo "Dump auto load..."
composer dump-autoload

echo "Discover packages..."
php artisan package:discover

echo "Run various artisan commands..."
php artisan cache:clear
php artisan migrate --no-interaction --force

php-fpm