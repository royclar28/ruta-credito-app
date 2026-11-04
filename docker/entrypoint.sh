#!/bin/bash
set -e

echo "==> Iniciando Ruta de Crédito App..."

# Asegurar APP_KEY si no está definida en las variables de entorno
if [ -z "$APP_KEY" ]; then
    echo "==> Generando APP_KEY en tiempo de ejecución..."
    php artisan key:generate --force
fi

# Esperar a que la base de datos esté lista si hay conexión configurada
echo "==> Verificando conexión a base de datos y ejecutando migraciones..."
php artisan migrate --force

echo "==> Ejecutando seeders de datos iniciales si no existen..."
php artisan db:seed --force || true

# Optimizar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Determinar puerto (Railway y Render inyectan la variable $PORT)
PORT=${PORT:-8000}
echo "==> Servidor escuchando en 0.0.0.0:$PORT"

exec php artisan serve --host=0.0.0.0 --port=$PORT
