#!/bin/bash
set -e

echo "-------INICIANDO DESPLIEGUE-------"

cd /var/www/Farusac_SistemaInformes

# 1. Bajar últimos cambios de Git
echo "Descargando código del repositorio..."
git pull origin main

cd Backend

# 2. Instalar/actualizar dependencias
echo "Actualizando paquetes de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Ejecutar migraciones pendientes
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force

# 4. Limpiar y reconstruir caché de producción
echo "Optimizando caché de Laravel..."
php artisan optimize

# 5. Asegurar permisos correctos
echo "Verificando permisos de almacenamiento..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "-------DESPLIEGUE COMPLETADO-------"