#!/bin/sh
set -e

# Inicializar el directorio de almacenamiento si está vacío
if [ ! "$(ls -A /var/www/storage)" ]; then
  echo "Inicializando directorio de almacenamiento..."
  cp -R /var/www/storage-init/. /var/www/storage
  chown -R www-data:www-data /var/www/storage
fi

# Elimidar directorio storage-init
rm -rf /var/www/storage-init

# Ejecutar migraciones de Laravel
php artisan migrate --force

# Limpiar la caché de configuración y rutas
php artisan config:cache
php artisan route:cache

# Ejecutar el comando por defecto
exec "$@"