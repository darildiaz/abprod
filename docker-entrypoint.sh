#!/bin/bash

# Copiar el código fuente
cp -r /var/www/* /var/www/html/
cd /var/www/html

# Crear directorios necesarios
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 777 storage bootstrap/cache

# Instalar dependencias como root
composer install --no-dev --ignore-platform-reqs

# Verificar si existe el archivo .env
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Generar clave de aplicación si no existe
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
fi

# Limpiar cachés de configuración
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cambiar permisos
chown -R laravel_user:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar herramienta pgrep
apt-get update && apt-get install -y procps

# Cambiar al usuario laravel_user
su laravel_user

# Iniciar Laravel Octane con Swoole
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8081 