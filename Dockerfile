FROM composer:2.7

WORKDIR /app

RUN apk add --no-cache git unzip

CMD ["bash"]
