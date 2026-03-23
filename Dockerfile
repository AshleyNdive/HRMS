FROM php:8.3-fpm

ARG user
ARG uid

# 1. Added 'install -y' and fixed the list of dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Fixed typo: pdo_myql -> pdo_mysql
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Create user
RUN useradd -u $uid -ms /bin/bash -g www-data $user

WORKDIR /var/www

# 4. Copy files with correct permissions
COPY --chown=$user:www-data . /var/www

USER $user

# 5. Fixed spacing
EXPOSE 9000
CMD ["php-fpm"]
