DOCKER_COMPOSE  = docker-compose

EXEC_PHP        = $(DOCKER_COMPOSE) exec -T php /entrypoint

SYMFONY         = $(EXEC_PHP) bin/console
COMPOSER        = $(EXEC_PHP) composer

##
## Project
## -------
##

build:
	$(DOCKER_COMPOSE) pull --parallel --quiet --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build --pull

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: build start vendor

reset: ## Stop and start a fresh install of the project
reset: kill install

start: ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf .env vendor

no-docker:
	$(eval DOCKER_COMPOSE := #)
	$(eval EXEC_PHP := )

.PHONY: build kill install reset start stop clean no-docker

##
## Utils
## -----
##

# rules based on files
composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction -vvv

vendor: composer.lock
	$(COMPOSER) install

php:
	$(DOCKER_COMPOSE) exec php /bin/sh

sf:
	$(SYMFONY) $(command)

test:
	$(EXEC_PHP) vendor/bin/simple-phpunit $(command)

analyse:
	$(DOCKER_COMPOSE) exec php ./vendor/bin/phpstan analyse src/ --level=$(level)


.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
