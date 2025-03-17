#!/usr/bin/env bash
set -e

host="$1"
shift
cmd="$@"

echo "Esperando a que MySQL esté disponible en $host..."
until mysql -h "$host" -u root -p"ja_Riz657tH]" -e "SELECT 1" &> /dev/null; do
  echo "MySQL aún no está listo - esperando..."
  sleep 2
done

echo "MySQL está listo! Ejecutando comando: $cmd"
exec $cmd
