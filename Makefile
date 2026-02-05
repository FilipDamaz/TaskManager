IMAGE=taskmanager-php

.PHONY: build init composer shell up down logs

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
