FROM php:8.3-cli

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    autoconf \
    g++ \
    make \
    libssl-dev \
    libtool \
    default-mysql-client

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    xml \
    gd \
    zip \
    bcmath

# Instalar Swoole
RUN pecl install swoole && docker-php-ext-enable swoole

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-interaction --optimize-autoloader

# Configurar permisos
RUN chmod -R 775 storage bootstrap/cache

# Copiar archivo de entorno
COPY .envDev .env

# Generar clave de aplicación
RUN php artisan key:generate

# Instalar Octane
RUN php artisan octane:install --server="swoole"

# Exponer puerto
EXPOSE 8100

# Iniciar aplicación
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8100", "--max-requests=500"]
