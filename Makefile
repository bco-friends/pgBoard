.DEFAULT: help
.PHONY: start stop build-up refresh php-shell

# Outputs list of available commands.
help:
	@echo The following commands are available: \
	"\n" start: "\t" Start the Docker container \
	"\n" stop: "\t\t" Stop the Docker container \
	"\n" refresh: "\t" Stop, rebuild, and restart the Docker container \
	"\n" php-shell: "\t" Open a shell session into the PHP container \
	"\n" db-seed: "\t" Seed the database with sample data. \

# Starts the Docker container.
start:
	docker-compose up -d

# Stops the Docker container.
stop:
	docker-compose down

# Builds the Docker container and starts it.
build-up:
	docker-compose up --build -d

init: init-files build-up db-drop db-create db-seed

init-files:
	cp config.default.php config.php && \
	cp .example.env .env && \
	cp lang/en.default.php lang/en.php && \
	cp class/Plugin.default.php class/Plugin.php

# Stops the Docker container if it's running, the rebuilds and restarts it.
refresh: stop build-up

# Opens a Bash session within the PHP container.
php-shell:
	docker exec -it pgb-php /bin/bash

db-shell:
	docker exec -it pgb-postgres psql -U postgres -w

phpstan:
	docker exec -it pgb-php vendor/bin/phpstan analyse index.php config.php error.php core.php class module

phpunit:
	docker exec -it pgb-php vendor/bin/phpunit

xdebug-on:
	docker exec -it pgb-php cp /var/www/html/docker/php/conf/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& docker restart pgb-php
xdebug-off:
	docker exec -it pgb-php rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && docker restart pgb-php

db-drop:
	docker exec -it pgb-postgres psql -U postgres -w -c "DROP DATABASE IF EXISTS board;"

db-create:
	docker exec -it pgb-postgres psql -U postgres -w -c "CREATE DATABASE board;" && \
	docker exec -it pgb-postgres psql -U postgres -w -d board -f ./etc/data/1-Schema.sql && \
	docker exec -it pgb-postgres psql -U postgres -w -d board -f ./etc/data/2-Functions.sql && \
	docker exec -it pgb-postgres psql -U postgres -w -d board -f ./etc/data/3-Indexes-FKeys-Triggers.sql

db-seed:
	docker exec -it pgb-php php bin/console.php db:seed --no-interaction
