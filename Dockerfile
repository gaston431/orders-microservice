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
    linux-headers autoconf g++ make openssl-dev

# Instalar las extensiones del núcleo indispensables para base de datos y colas (incluye sockets)
RUN docker-php-ext-install pdo pdo_mysql bcmath sockets

# Extensión nativa de MongoDB vía PECL
# Extensiones PECL
RUN pecl install mongodb xdebug \
    && docker-php-ext-enable mongodb xdebug

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    
# Instalar Composer copiándolo desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo estándar
WORKDIR /var/www

# Copiar el código del proyecto al contenedor
COPY . /var/www

# Instalar dependencias puras de producción utilizando tu composer.json limpio --no-dev
RUN composer install --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts

# Aplicar los permisos estandarizados para el almacenamiento y la caché de Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto estándar de comunicación web
EXPOSE 80

# Iniciar comando nativo unificado para levantar el servicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
