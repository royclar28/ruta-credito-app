FROM php:8.4-cli-alpine

# Utilizar el instalador oficial de extensiones PHP (optimizado para Alpine)
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Instalar dependencias base y extensiones PHP en un solo paso
RUN apk add --no-cache bash curl git && \
    install-php-extensions pdo_pgsql pgsql zip intl bcmath opcache @composer

WORKDIR /app

# Copiar manifiestos de dependencias
COPY composer.json composer.lock ./

# Instalar dependencias de producción
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar el código fuente completo
COPY . .

# Crear el archivo .env garantizado desde el build y generar APP_KEY
RUN cp .env.example .env && php artisan key:generate --force

# Autoloader optimizado
RUN composer dump-autoload --optimize

# Permisos requeridos por Laravel y archivo sqlite inicial
RUN mkdir -p database && touch database/database.sqlite && chmod -R 777 database storage bootstrap/cache

# Configurar script de inicio
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
