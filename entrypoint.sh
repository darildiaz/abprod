#!/bin/bash
set -e

# Asegurar que los directorios existan con permisos correctos
mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear archivo .env con clave predefinida si no existe
if [ ! -f ".env" ]; then
    echo "Creando archivo .env..."
    cat > .env << 'EOL'
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
rm -f ./public/storage
echo "Enlazando almacenamiento correctamente..."
ln -sf /var/www/html/storage/app/public ./public/storage

# Arreglar problemas de PSR-4 sin usar artisan
echo "Corrigiendo archivos de políticas..."
find ./app/Policies -name "*Policy.php" -exec bash -c 'filename=$(basename "$1"); uppercase_filename="$(echo ${filename:0:1} | tr "[:lower:]" "[:upper:]")${filename:1}"; if [ "$filename" != "$uppercase_filename" ]; then mv "$1" "$(dirname "$1")/$uppercase_filename"; fi' bash {} \; 2>/dev/null || true

# Eliminar y recrear archivo EditProfile.php si existe con namespace incorrecto
if [ -f "./app/Filament/Pages/EditProfile.php" ]; then
    mkdir -p ./app/Filament/Pages/Auth
    mv ./app/Filament/Pages/EditProfile.php ./app/Filament/Pages/Auth/EditProfile.php 2>/dev/null || true
fi

# Instalar dependencias si vendor no existe
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias..."
    composer install --no-interaction --prefer-dist --optimize-autoloader || composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
fi

# Asegurar que composer.lock existe
if [ ! -f "composer.lock" ]; then
    echo "Creando composer.lock..."
    composer update --no-interaction
fi

# Verificar si vendor/autoload.php existe antes de ejecutar comandos Artisan
if [ -f "vendor/autoload.php" ]; then
    # Limpiar caché
    echo "Limpiando caché..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    php artisan config:cache
    php artisan optimize
else
    echo "El archivo vendor/autoload.php no existe. Saltando comandos Artisan."
fi

# Ejecutar comandos adicionales si es necesario
exec "$@" 