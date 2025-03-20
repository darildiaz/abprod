#!/bin/bash
set -e

echo "Deteniendo contenedores existentes..."
docker compose down -v

echo "Eliminando archivos de caché..."
rm -rf bootstrap/cache/*
rm -rf vendor
rm -rf node_modules

echo "Reconstruyendo y arrancando contenedores..."
docker compose build --no-cache
docker compose up -d

echo "Mostrando logs (Ctrl+C para salir)..."
docker compose logs -f app 