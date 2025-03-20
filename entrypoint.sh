#!/bin/bash
set -e

# Asegurar que los directorios existan con permisos correctos
mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear archivo .env con clave predefinida si no existe
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creando archivo .env..."
    cat > /var/www/html/.env << 'EOL'
APP_NAME=ABPROD
APP_ENV=local
APP_KEY=base64:e9aI+UNsQH3SFb84o4aslf0LWxEnqNJXQ5aWHS6WQBQ=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=abprod
DB_USERNAME=abprod
DB_PASSWORD=ja_Riz657tH]

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOL
fi

# Asegurar que el directorio de almacenamiento público existe
mkdir -p /var/www/html/storage/app/public

# Eliminar y recrear enlace simbólico
rm -f /var/www/html/public/storage
echo "Enlazando almacenamiento correctamente..."
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Instalar dependencias si vendor no existe
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Instalando dependencias..."
    cd /var/www/html && composer install --no-interaction --prefer-dist --optimize-autoloader || composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
fi

# Asegurar que composer.lock existe
if [ ! -f "/var/www/html/composer.lock" ]; then
    echo "Creando composer.lock..."
    cd /var/www/html && composer update --no-interaction
fi

# Verificar si vendor/autoload.php existe antes de ejecutar comandos Artisan
if [ -f "/var/www/html/vendor/autoload.php" ]; then
    # Limpiar caché
    echo "Limpiando caché..."
    cd /var/www/html && php artisan config:clear
    cd /var/www/html && php artisan route:clear
    cd /var/www/html && php artisan view:clear
    cd /var/www/html && php artisan cache:clear
    cd /var/www/html && php artisan config:cache
    cd /var/www/html && php artisan optimize
else
    echo "El archivo vendor/autoload.php no existe. Saltando comandos Artisan."
fi

# Ejecutar Apache en primer plano
exec apache2-foreground 