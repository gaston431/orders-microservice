FROM php:8.4-fpm-alpine

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
    oniguruma-dev \
    linux-headers

# Instalar las extensiones del núcleo indispensables para base de datos y colas (incluye sockets)
RUN docker-php-ext-install pdo pdo_mysql bcmath sockets

# Instalar Composer copiándolo desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo estándar
WORKDIR /var/www

# Copiar el código del proyecto al contenedor
COPY . /var/www

# Instalar dependencias puras de producción utilizando tu composer.json limpio
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Aplicar los permisos estandarizados para el almacenamiento y la caché de Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto estándar de comunicación web
EXPOSE 80

# Iniciar comando nativo unificado para levantar el servicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
