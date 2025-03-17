# Usar una imagen base de PHP con FPM
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    linux-headers \
    bash \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    npm \
    mysql-client \
    autoconf \
    g++ \
    make \
    gcc \
    libtool

# Instalar y configurar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    xml \
    pcntl \
    gd \
    zip \
    sockets \
    bcmath \
    intl

# Instalar Swoole para Laravel Octane
RUN pecl install swoole
RUN docker-php-ext-enable swoole

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de composer primero para aprovechar la caché
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev

# Copiar el resto de los archivos del proyecto
COPY . .

# Generar autoloader optimizado
RUN composer dump-autoload --optimize --no-dev

# Configurar permisos
RUN mkdir -p /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views
RUN chmod -R 775 /app/storage /app/bootstrap/cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copiar script de espera para MySQL
COPY wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# Copiar archivo de entorno
COPY .envDev .env

# Instalar Octane
RUN php artisan octane:install --server="swoole"

# Exponer puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD ["sh", "-c", "wait-for-it mysql:3306 -- php artisan migrate --force && php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000"]
