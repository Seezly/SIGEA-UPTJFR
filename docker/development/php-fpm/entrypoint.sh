#!/bin/sh
set -e

# Limpiar caché y configuraciones
echo "Limpiando caché y configuraciones..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecutar el comando por defecto
exec "$@"