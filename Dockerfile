FROM php:8.3-fpm

ARG user
ARG uid

# 1. Installation des dépendances système + libicu et libzip pour Filament
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Installation des extensions PHP (ajout de intl et zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Création de l'utilisateur
RUN useradd -u $uid -ms /bin/bash -g www-data $user

WORKDIR /var/www

# 4. Copie des fichiers avec les bonnes permissions
COPY --chown=$user:www-data . /var/www

USER $user

EXPOSE 9000
CMD ["php-fpm"]
