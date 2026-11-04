#!/bin/bash
set -e

echo "==> [$(date '+%Y-%m-%d %H:%M:%S')] Iniciando Ruta de Crédito App v1.1..."

# 1. Asegurar existencia de archivo .env en el contenedor
if [ ! -f /app/.env ]; then
    echo "==> Generando archivo .env base..."
    if [ -f /app/.env.example ]; then
        cp /app/.env.example /app/.env
    else
        touch /app/.env
    fi
fi

# 2. Configurar conexión a base de datos (PostgreSQL si existe DATABASE_URL, de lo contrario SQLite autónomo)
if [ -n "$DATABASE_URL" ] || [ -n "$DB_URL" ]; then
    export DB_CONNECTION=${DB_CONNECTION:-pgsql}
else
    export DB_CONNECTION=${DB_CONNECTION:-sqlite}
    touch /app/database/database.sqlite
fi

export SESSION_DRIVER=${SESSION_DRIVER:-database}
export APP_NAME=${APP_NAME:-"Ruta de Crédito"}

# 3. Asegurar APP_KEY si por alguna razón no estuviera generada
if [ -z "$APP_KEY" ] && ! grep -q "APP_KEY=base64:" /app/.env 2>/dev/null; then
    echo "==> Generando APP_KEY en tiempo de ejecución..."
    php artisan key:generate --force || true
fi

# 4. Reintentar conexión a la base de datos y correr migraciones (espera a que PostgreSQL esté listo)
echo "==> Verificando conexión a base de datos y ejecutando migraciones..."
MAX_RETRIES=15
RETRY_COUNT=0
MIGRATED=0

until [ $RETRY_COUNT -ge $MAX_RETRIES ]; do
    if php artisan migrate --force; then
        MIGRATED=1
        break
    fi
    RETRY_COUNT=$((RETRY_COUNT+1))
    echo "==> Esperando conexión a base de datos... ($RETRY_COUNT/$MAX_RETRIES). Reintentando en 3s..."
    sleep 3
done

if [ $MIGRATED -eq 1 ]; then
    echo "==> Base de datos lista. Ejecutando seeders de datos iniciales..."
    php artisan db:seed --force || true
else
    echo "==> Advertencia: Las migraciones no pudieron ejecutarse tras $MAX_RETRIES intentos."
fi

# 5. Limpieza de cachés para evitar inconsistencias
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Puerto dinámico provisto por Railway / Render
PORT=${PORT:-8000}
echo "==> Servidor escuchando en 0.0.0.0:$PORT"

exec php artisan serve --host=0.0.0.0 --port=$PORT
