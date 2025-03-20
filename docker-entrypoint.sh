#!/bin/bash

# Instalar herramienta pgrep
apt-get update && apt-get install -y procps

# Crear directorio de trabajo si no existe
mkdir -p /var/www/html

# Copiar el código fuente (evitando copiar el directorio en sí mismo)
cp -r /var/www/* /var/www/html/ 2>/dev/null || true

# Ir al directorio de trabajo
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
php artisan cache:clear

# Optimizar autoloader
composer dump-autoload -o

# Corregir permisos
chown -R laravel_user:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ejecutar script de corrección de políticas
chmod +x /var/www/fix-policies.sh
/var/www/fix-policies.sh

# Cambiar al usuario laravel_user
su laravel_user

# Iniciar Laravel Octane con Swoole
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8081 