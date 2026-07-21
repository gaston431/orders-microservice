FROM php:8.4-fpm-alpine

RUN apk add --no-cache linux-headers

# Instalar extensiones mínimas del núcleo para persistencia de datos
RUN docker-php-ext-install pdo_mysql bcmath sockets

# Instalar Composer copiándolo desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir el directorio de trabajo interno
WORKDIR /var/www

# Copiar los archivos del proyecto al contenedor
COPY . /var/www

# Instalar dependencias puras saltando los ganchos automáticos de Artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts

# Purgar cualquier archivo de caché corrupto heredado del entorno local
RUN rm -rf bootstrap/cache/*.php storage/framework/cache/data/* storage/framework/views/*.php

# Asignar permisos estándar al usuario del servidor web de Alpine (www-data)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Exponer el puerto de comunicación del contenedor
EXPOSE 80

# Servidor web nativo de un solo hilo, ideal y 100% estable para microservicios locales en Docker
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
