#!/bin/bash
set -e

# Esperar a que la base de datos esté lista
echo "Esperando conexión a la base de datos..."
sleep 5

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (solo la primera vez)
if [ ! -f /var/www/html/storage/.seeded ]; then
    php artisan db:seed --force
    touch /var/www/html/storage/.seeded
fi

# Cache de configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear link de storage
php artisan storage:link 2>/dev/null || true

echo "Aplicación lista!"

# Ejecutar comando original
exec "$@"
