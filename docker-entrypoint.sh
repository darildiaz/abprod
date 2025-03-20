#!/bin/bash

# Copiar el código fuente
cp -r /var/www/* /var/www/html/
cd /var/www/html

# Crear directorios necesarios
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 777 storage bootstrap/cache

# Instalar dependencias como root
composer install --no-dev --ignore-platform-reqs

# Cambiar permisos
chown -R laravel_user:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cambiar al usuario laravel_user
su laravel_user

# Iniciar Laravel Octane con Swoole
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8081 