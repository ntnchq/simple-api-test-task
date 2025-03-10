FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql sockets

RUN php -m | grep sockets || { echo "sockets extension not installed"; exit 1; }

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=ghcr.io/roadrunner-server/roadrunner:latest /usr/bin/rr /usr/bin/rr

WORKDIR /app

CMD ["/usr/bin/rr", "serve"] 