<<<<<<< HEAD
FROM php:8.3-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql intl zip

# Configurar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# Configurar virtualhost
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar script de entrada y hacerlo ejecutable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
=======
FROM elrincondeisma/php-for-laravel:8.3.7

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --ignore-platform-reqs
RUN composer require laravel/octane
RUN mkdir -p /var/www/html/storage/logs

# Crear archivo .env con clave predefinida
RUN cat > .env << 'EOL'
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

# Configurar permisos
RUN chmod -R 777 storage bootstrap/cache

# Instalar Swoole
RUN php artisan octane:install --server="swoole"
>>>>>>> 262696ff2e92ccb06359e127e58d36b9f5c35d37

# Configurar límites de memoria de PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

<<<<<<< HEAD
# Crear script de configuración simple que no requiere artisan
RUN echo '#!/bin/bash\n\
mkdir -p /var/www/html/storage/app/public\n\
mkdir -p /var/www/html/storage/logs\n\
mkdir -p /var/www/html/storage/framework/cache\n\
mkdir -p /var/www/html/storage/framework/sessions\n\
mkdir -p /var/www/html/storage/framework/views\n\
mkdir -p /var/www/html/bootstrap/cache\n\
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache\n\
rm -f /var/www/html/public/storage\n\
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage\n\
exec apache2-foreground\n\
' > /usr/local/bin/init-laravel.sh \
&& chmod +x /usr/local/bin/init-laravel.sh

# Exponer puerto para Apache
EXPOSE 80

# Comando predeterminado
CMD ["/usr/local/bin/init-laravel.sh"]
=======
# Comando por defecto
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8081"]
>>>>>>> 262696ff2e92ccb06359e127e58d36b9f5c35d37
