#!/bin/bash

echo "=== Iniciando despliegue de ABProd ==="

# Verificar si se quiere usar la versión simple
if [ "$1" == "simple" ]; then
  echo "Usando configuración simplificada..."
  
  # Construir la imagen usando Dockerfile.simple
  echo "Construyendo imagen Docker..."
  docker build -f Dockerfile.simple -t abprod .
  
  # Ejecutar el contenedor
  echo "Ejecutando contenedor..."
  docker run -it -p 8100:8100 abprod
else
  # Usar docker-compose completo
  echo "Usando configuración completa con docker-compose..."
  
  # Detener contenedores existentes
  echo "Deteniendo contenedores existentes..."
  docker-compose down
  
  # Construir y ejecutar los contenedores
  echo "Construyendo y ejecutando contenedores..."
  docker-compose up --build -d
  
  echo "Servicios disponibles en:"
  echo "- Laravel: http://localhost:8100"
  echo "- phpMyAdmin: http://localhost:8080"
fi

echo "=== Despliegue completado ===" 