#!/usr/bin/env bash
# Script de build para Render.com

echo "🚀 Iniciando build para Render..."

# Salir si hay algún error
set -o errexit

# Instalar dependencias de Composer
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Limpiar cachés anteriores
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cachear configuración para producción
echo "⚡ Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace simbólico para storage (si no existe)
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link || true

echo "✅ Build completado exitosamente!"
