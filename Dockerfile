FROM php:8.3-apache

WORKDIR /var/www/html

# Mod Rewrite
RUN a2enmod rewrite

# Linux Library
RUN apt-get update -y && apt-get install -y \
    git \
    vim \
    curl \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# Composer
COPY --from=composer:2.2.6 /usr/bin/composer /usr/bin/composer

# PHP Extension
RUN docker-php-ext-install gettext intl pdo_mysql gd

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

RUN apt-get install -y libzip-dev \
    && docker-php-ext-install zip

# Install npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

COPY package-lock.json package.json ./
RUN npm install

# Comment out the [openssl_init] section in the OpenSSL configuration file
RUN sed -i 's/^\[openssl_init\]/#&/' /etc/ssl/openssl.cnf

# Add the providers section or any other modifications as needed
RUN echo "\n\
    # Adding the provider_sect\n\
    [provider_sect]\n\
    default = default_sect\n\
    legacy = legacy_sect\n\
    \n\
    [default_sect]\n\
    activate = 1\n\
    \n\
    [legacy_sect]\n\
    activate = 1\n" >> /etc/ssl/openssl.cnf

# Copy existing application code to the container
COPY --chown=www-data:www-data . .

USER www-data

RUN composer install --optimize-autoloader --no-dev --ignore-platform-req=ext-exif --ignore-platform-req=ext-exif --ignore-platform-req=ext-exif
RUN npm run build

# Cache resources
RUN php artisan event:cache && \
    php artisan route:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan livewire:publish --assets

# Link public folder
RUN php artisan storage:link

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

