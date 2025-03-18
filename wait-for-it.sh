#!/usr/bin/env bash
set -e

host="$1"
shift
cmd="$@"

until mysql -h "$host" -u root -proot -e "SELECT 1" &> /dev/null; do
  echo "Esperando a que MySQL esté listo..."
  sleep 2
done

exec $cmd
