.DEFAULT: help
.PHONY: start stop build-up refresh php-shell

# Outputs list of available commands.
help:
	@echo The following commands are available: \
	"\n" start: Start the Docker container \
	"\n" stop: Stop the Docker container \
	"\n" refresh: Stop, rebuild, and restart the Docker container \
	"\n" php-shell: Open a shell session into the PHP container

# Starts the Docker container.
start:
	docker-compose up

# Stops the Docker container.
stop:
	docker-compose down

# Builds the Docker container and starts it.
build-up:
	docker-compose up --build

# Stops the Docker container if it's running, the rebuilds and restarts it.
refresh: stop build-up

# Opens a Bash session within the PHP container.
php-shell:
	docker exec -it pgb-php /bin/bash
