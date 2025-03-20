#!/bin/bash

echo "Deteniendo y eliminando todos los contenedores..."
docker-compose down -v

echo "Eliminando imágenes existentes..."
docker rmi -f $(docker images -q abprod_app) 2>/dev/null || true

echo "Eliminando volúmenes huérfanos..."
docker volume prune -f

echo "Reconstruyendo y levantando contenedores..."
docker-compose build --no-cache
docker-compose up -d

echo "Esperando a que los servicios estén disponibles..."
sleep 5

echo "Mostrando logs del contenedor de la aplicación..."
docker logs abprod_app

echo "Proceso completado. La aplicación debería estar disponible en:"
echo "- Laravel: http://localhost:8081"
echo "- phpMyAdmin: http://localhost:8080" 