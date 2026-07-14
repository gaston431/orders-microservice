FROM php:8.3-fpm-alpine

# Instalar extensiones de PHP requeridas y herramientas del sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_mysql bcmath

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar el código del proyecto
COPY . /var/www

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos para almacenamiento y caché de Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto del contenedor (Nginx interno)
EXPOSE 80

# Iniciar comando personalizado para levantar Nginx y PHP-FPM juntos
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
