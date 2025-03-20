FROM php:8.3-fpm

ARG user
ARG uid

# Establecer el directorio de trabajo
WORKDIR /var/www

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
    libgd3 \
    libgd-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml zip intl

# Instalar Swoole
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario del sistema
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Configurar PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# Configurar Git para directorios seguros
RUN git config --global --add safe.directory /var/www

# Set working directory
WORKDIR /var/www

# Copiar código fuente
COPY . /var/www
RUN chown -R $user:$user /var/www

# Establecer usuario por defecto
USER $user

# Exponer puerto
EXPOSE 8081

# Iniciar Laravel Octane con Swoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8081"]
