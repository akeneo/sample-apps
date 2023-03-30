APP_NAME = app
export DEV_UID = $(shell id -u)
export DEV_GID = $(shell id -g)
DOCKER_COMPOSE = docker compose
DOCKER_BUILD = BUILDKIT=1 $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run
DOCKER_LOGS = $(DOCKER_COMPOSE) logs
APP_ADDRESS = http://localhost:8081/

install: ## Install PHP sample App
	@$(MAKE) requirements
	@$(MAKE) post-requirements
	@$(MAKE) _display

requirements: ## Check all requirements before building and starting the App
	@../../../common/bin/install/checkRequirements.sh
	@../../../common/bin/install/dotEnvFileCreator.sh

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

logs: ## output apache logs
	$(DOCKER_LOGS) -f --no-log-prefix --tail 20

.PHONY: all tests clean
tests: ## run phpunit tests
	@$(DOCKER_EXEC) $(APP_NAME) php bin/phpunit

_display: ##
	@echo ""
	@echo "**** PHP Symfony Sample App ****"
	@echo ""
	@echo "     Web app (served by PHP Apache 2 with hot reload): $(APP_ADDRESS)"
	@echo "     Activate URL : $(APP_ADDRESS)activate"
	@echo "     Callback URL : $(APP_ADDRESS)callback"
	@echo "     First API call URL : $(APP_ADDRESS)first-api-call"
