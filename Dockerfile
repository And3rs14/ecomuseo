# Utiliza la imagen oficial de PHP 8.2 como imagen base
FROM php:8.2-fpm

# Establece el directorio de trabajo
WORKDIR /var/www

# Instala las dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instala extensiones de PHP adicionales
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl

# Instala extensiones de PHP recomendadas para Laravel
RUN docker-php-ext-install bcmath

# Limpia la caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el contenido del directorio de la aplicación al directorio de trabajo
COPY . /var/www

# Asegura que existan los directorios necesarios para Laravel
RUN mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/bootstrap/cache

# Ajusta los permisos para Laravel
RUN chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage \
    && chown -R www-data:www-data /var/www/bootstrap/cache

# Cambia el usuario actual a www-data
USER www-data

# Expone el puerto 9000 y inicia el servidor php-fpm
EXPOSE 9000
CMD ["php-fpm"]
