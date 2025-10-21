# Imagen base
FROM php:8.3-fpm

# Instalar dependencias y Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    libzip-dev \
    nginx

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP, incluyendo zip, pgsql y pdo_pgsql para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la aplicación
COPY . /var/www

# Instalar dependencias de Composer
# RUN composer install

# Instalar dependencias de npm y construir assets
# RUN npm install && npm run build

# Copiar configuración de Nginx
COPY .docker/nginx/default.conf /etc/nginx/nginx.conf

# Exponer puerto 80
EXPOSE 80

# agregar permisos
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Crear script de inicio
RUN echo '#!/bin/sh' > /usr/local/bin/start-container.sh \
    && echo 'set -e' >> /usr/local/bin/start-container.sh \
    && echo 'nginx' >> /usr/local/bin/start-container.sh \
    && echo 'php-fpm' >> /usr/local/bin/start-container.sh \
    && chmod +x /usr/local/bin/start-container.sh

# Iniciar Nginx y PHP-FPM
CMD ["/usr/local/bin/start-container.sh"]
