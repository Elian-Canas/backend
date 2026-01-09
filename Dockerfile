FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    zip unzip git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www

# 👇 Copiar SOLO los archivos necesarios para Composer (no todo el proyecto)
COPY composer.json composer.lock ./

# 👇 Instalar dependencias primero (aprovecha caché de Docker)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 👇 Ahora copiar el resto del código
COPY . .

# 👇 No necesitas composer install de nuevo ni dump-autoload (ya se hizo en install)
# Pero si quieres asegurarte (opcional):
RUN composer dump-autoload --optimize

# Permisos
RUN chown -R www-data:www-data /var/www