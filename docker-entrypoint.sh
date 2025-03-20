#!/bin/bash

# Verificar si procps ya está instalado antes de intentar instalarlo
if ! dpkg -l | grep -q procps; then
    apt-get update && apt-get install -y procps
fi

# Crear directorio de trabajo si no existe
mkdir -p /var/www/html

# Configurar Git para permitir el directorio como seguro (ejecutar como root)
git config --system --add safe.directory /var/www/html
git config --global --add safe.directory /var/www/html

# Copiar el código fuente (evitando copiar el directorio en sí mismo)
find /var/www -maxdepth 1 -not -path "/var/www" -not -path "/var/www/html" -exec cp -rf {} /var/www/html/ \;

# Ir al directorio de trabajo
cd /var/www/html

# Crear directorios necesarios
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Ejecutar script de corrección de políticas primero
chmod +x /var/www/fix-policies.sh
/var/www/fix-policies.sh

# Instalar dependencias como root
composer install --no-dev --ignore-platform-reqs

# Crear un nuevo archivo .env con una clave de aplicación predefinida
cat > .env << 'EOL'
APP_NAME=ABPROD
APP_ENV=local
APP_KEY=base64:e9aI+UNsQH3SFb84o4aslf0LWxEnqNJXQ5aWHS6WQBQ=
APP_DEBUG=true
APP_URL=http://localhost:8081

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

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOL

# Corregir permisos
chown -R laravel_user:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache

# Optimizar autoloader
composer dump-autoload -o

# Cambiar al usuario laravel_user
su laravel_user

# Iniciar Laravel Octane con Swoole
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8081 