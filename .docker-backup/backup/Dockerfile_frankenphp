# Usamos la imagen base de FrankenPHP
FROM dunglas/frankenphp:latest

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    openssl \
    pkg-config \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libonig-dev \
    libmemcached-dev \
    libz-dev \
    libwebp-dev \
    libxpm-dev \
    libmcrypt-dev \
    libicu-dev \
    libzip-dev\
    zlib1g-dev \
    g++ \
    gnupg \
    procps \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP una por una para depuración
RUN install-php-extensions pcntl
RUN docker-php-ext-install pdo_pgsql pgsql bcmath curl mbstring zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && docker-php-ext-install gd
# RUN docker-php-ext-install json

# redis
RUN pecl install redis && docker-php-ext-enable redis
# pcov
RUN pecl install pcov && docker-php-ext-enable pcov

# RUN docker-php-ext-install tokenizer
# RUN docker-php-ext-install xml

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . /app

# Instalar dependencias de la aplicación
RUN composer install --no-dev --optimize-autoloader

# Cambiar permisos de las carpetas de Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Comando por defecto
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--max-requests=5"]
