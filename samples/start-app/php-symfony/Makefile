APP_NAME = app
DOCKER_COMPOSE = docker-compose
DOCKER_BUILD = $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run
APP_ADDRESS = http://localhost:8082/

install: ## Install PHP sample App
	@$(MAKE) requirements
	@$(MAKE) post-requirements
	@$(MAKE) _display

requirements: ## Check all requirements before building and starting the App
	@bin/install/checkRequirements.sh
	@bin/install/dotEnvFileCreator.sh

post-requirements: build
	@$(MAKE) start
	@echo "Installing dependencies"
	@$(DOCKER_EXEC) $(APP_NAME) composer install
	@echo "Installing SQLite migrations"
	@$(DOCKER_EXEC) $(APP_NAME) php bin/console doctrine:migrations:migrate --no-interaction

build: ## build docker image
	$(DOCKER_BUILD)

start: ## start docker image
	$(DOCKER_UP)
	@$(MAKE) _display

stop: ## stop docker image
	$(DOCKER_DOWN)

_display: ##
	@echo ""
	@echo "**** PHP Symfony Sample App ****"
	@echo ""
	@echo "     Web app (served by PHP Apache 1 - with hot reload): https://localhost:8082"
