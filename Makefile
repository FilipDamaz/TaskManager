IMAGE=taskmanager-php

.PHONY: build init composer shell up down logs migrate import-users long-test jwt-keys
 
test:
\tdocker run --rm -u 1000:1000 -v $(PWD):/app -w /app $(IMAGE) php bin/phpunit $(ARGS)

build:
\tdocker build -t $(IMAGE) .

init: build
\tdocker run --rm -u 1000:1000 -v $(PWD):/app -w /app $(IMAGE) \
\t\tsh -lc "composer create-project symfony/skeleton:\"7.4.*\" /tmp/app && cp -a /tmp/app/. /app/"

composer:
\tdocker run --rm -u 1000:1000 -v $(PWD):/app -w /app $(IMAGE) composer $(ARGS)

shell:
\tdocker run -it --rm -v $(PWD):/app -w /app $(IMAGE) bash

up:
\tdocker compose up -d

down:
\tdocker compose down

logs:
\tdocker compose logs -f --tail=200

migrate:
\tdocker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

import-users:
\tdocker compose exec app php bin/console app:users:import

long-test:
\tdocker compose exec app sh -lc "APP_ENV=integration php bin/console doctrine:database:create --if-not-exists"
\tdocker compose exec app sh -lc "APP_ENV=integration php bin/console doctrine:migrations:migrate --no-interaction"
\tdocker compose exec app sh -lc "APP_ENV=integration php bin/phpunit -c phpunit.integration.xml"

jwt-keys:
\tdocker compose exec app sh -lc "mkdir -p var/jwt && openssl genpkey -algorithm RSA -out var/jwt/private.pem -pkeyopt rsa_keygen_bits:4096 && openssl pkey -in var/jwt/private.pem -out var/jwt/public.pem -pubout"
