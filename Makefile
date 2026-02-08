.DEFAULT: help
.PHONY: start stop build-up rebuild php-shell

# Outputs list of available commands.
help:
	@echo The following commands are available: \
	"\n" start: "\t" Start the Docker container \
	"\n" stop: "\t\t" Stop the Docker container \
	"\n" rebuild: "\t" Stop, rebuild, and restart the Docker container \
	"\n" php-shell: "\t" Open a shell session into the PHP container \
	"\n" db-seed: "\t" Seed the database with sample data. \

# Starts the Docker container.
start:
	docker compose up -d

# Stops the Docker container.
stop:
	docker compose down

# Builds the Docker container and starts it.
build-up:
	docker compose up --build -d

composer-init:
	docker exec -it pgboard_web /usr/bin/composer install

init: init-files build-up composer-init db-drop db-create db-seed

init-files:
	@mkdir -p "./config/lang" "./config/class" && \
	cp "./.example.env" "./.env" && \
	[ ! -f "./config/config.php" ] && cp "./config.default.php" "./config/config.php" || true && \
	[ ! -f "./config/lang/en.php" ] && cp "./lang/en.default.php" "./config/lang/en.php" || true && \
	[ ! -f "./config/lang/en_header.php" ] && cp "./lang/en_header.default.php" "./config/lang/en_header.php" || true && \
	[ ! -f "./config/lang/en_footer.php" ] && cp "./lang/en_footer.default.php" "./config/lang/en_footer.php" || true && \
	[ ! -f "./config/class/Plugin.php" ] && cp "./class/Plugin.default.php" "./config/class/Plugin.php" || true


# Stops the Docker container if it's running, the rebuilds and restarts it.
rebuild: stop build-up

# Opens a Bash session within the PHP container.
php-shell:
	docker exec -it pgboard_web /bin/bash

db-shell:
	docker exec -it pgboard_db psql -U board -d board

phpstan:
	docker exec -it pgboard_web vendor/bin/phpstan analyse index.php config.php error.php core.php class module

phpunit:
	docker exec -it pgboard_web vendor/bin/phpunit

xdebug-on:
	docker exec -it pgboard_web cp /var/www/html/docker/php/conf/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& docker restart pgboard_web
xdebug-off:
	docker exec -it pgboard_web rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && docker restart pgboard_web

# Drop the existing database and create a fresh install.
db-refresh: db-drop db-create

db-reseed: db-refresh db-seed

db-drop:
	docker exec -it pgboard_db psql -U board -d postgres -c "DROP DATABASE IF EXISTS board;"

db-create:
	docker exec -it pgboard_db psql -U board -d postgres -c "CREATE DATABASE board;" && \
	docker exec -it pgboard_db psql -U board -d board -f /docker-entrypoint-initdb.d/1-Schema.sql && \
	docker exec -it pgboard_db psql -U board -d board -f /docker-entrypoint-initdb.d/2-Functions.sql && \
	docker exec -it pgboard_db psql -U board -d board -f /docker-entrypoint-initdb.d/3-Indexes-FKeys-Triggers.sql

db-seed:
	docker exec -it pgboard_web php bin/console.php db:seed --no-interaction
