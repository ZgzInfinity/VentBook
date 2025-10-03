#!/bin/bash

UID = $(shell id -u)
DOCKER_BE = avoris-php84-symfony62

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

start: ## Start the containers
	docker network create avoris-php84-symfony62-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker compose up -d

stop: ## Stoip the containers
	U_ID=${UID} docker compose stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) start

build: ## Rebuilds all the containers
	docker network create application_network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker compose build

upbuild: ## Levanta los contenedores forzando build (--build)
	docker network create avoris-php84-symfony62-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker compose build --no-cache
	U_ID=${UID} docker compose up -d

prepare: ## Runs backend commands
	$(MAKE) composer-install

run: ## starts the Symfony development server
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash -c "cd myapp && symfony local:serve:start --no-tls --allow-all-ip"

logs: ## Show Symfony logs in real time
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} symfony server:log

# Backend commands
composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} composer install --no-interaction
# End backend commands

ssh-be: ## bash into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash
	
code-style: ## Runs php-cs to fix code styling following Symfony rules
	U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony