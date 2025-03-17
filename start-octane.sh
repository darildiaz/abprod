#!/bin/bash

# Esperar a que MySQL esté listo
if [ -n "$DB_HOST" ]; then
    echo "Esperando a que MySQL esté disponible..."
    while ! mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null; do
        echo "MySQL aún no está listo - esperando..."
        sleep 2
    done
    echo "MySQL está listo!"
fi

# Ejecutar migraciones si es necesario
if [ -n "$DB_HOST" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
fi

# Iniciar Octane
echo "Iniciando Octane con Swoole..."
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8100 --workers=4 --max-requests=500 