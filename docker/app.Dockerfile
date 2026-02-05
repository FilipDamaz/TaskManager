FROM php:8.3-cli-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
