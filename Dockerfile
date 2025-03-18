# Usar una imagen base de PHP con FPM
FROM php:8.3-fpm

# Establecer el directorio de trabajo
WORKDIR /app

# Instalar herramientas necesarias
RUN apt-get update && apt-get install -y \
    apt-utils \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    pkg-config \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring gd xml zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader
RUN composer require laravel/octane

# Crear directorios de Laravel
RUN mkdir -p /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views
RUN chmod -R 777 /app/storage /app/bootstrap/cache

# Instalar Swoole para Laravel Octane
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Copiar archivo de entorno
COPY .envDev .env

# Instalar Octane
RUN php artisan octane:install --server="swoole"

# Esperar a que MySQL esté listo antes de ejecutar Laravel
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# Comando para arrancar Laravel con Octane
CMD ["sh", "-c", "wait-for-it mysql:3306 -- php artisan migrate --force && php artisan octane:start --server=swoole --host=0.0.0.0"]

# Exponer el puerto de Laravel
EXPOSE 8000
