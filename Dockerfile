FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev zip unzip curl git \
    && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
