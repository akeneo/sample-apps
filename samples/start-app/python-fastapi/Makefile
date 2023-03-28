APP_NAME = app
export DEV_UID = $(shell id -u)
export DEV_GID = $(shell id -g)
DOCKER_COMPOSE = docker compose
DOCKER_BUILD = $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run
APP_ADDRESS = http://localhost:8081/

install: ## Install Python sample App
	@$(MAKE) requirements
	@$(MAKE) build
	@$(MAKE) start
	@$(MAKE) _display

requirements: ## Check all requirements before building and starting the App
	@../../../common/bin/install/checkRequirements.sh
	@../../../common/bin/install/dotEnvFileCreator.sh

build: ## build docker image
	$(DOCKER_BUILD)

start: ## start docker image
	$(DOCKER_UP)
	@$(MAKE) _display

stop: ## stop docker image
	$(DOCKER_DOWN)

tests:
	$(DOCKER_EXEC) $(APP_NAME) python -m unittest discover

bash:
	$(DOCKER_EXEC) $(APP_NAME) bash

_display: ##
	@echo ""
	@echo "**** Python + FastApi Sample App ****"
	@echo ""
	@echo "     Web app (served by Python + FastApi with hot reload): $(APP_ADDRESS)"
	@echo "     Activate URL : $(APP_ADDRESS)activate"
	@echo "     Callback URL : $(APP_ADDRESS)callback"
	@echo "     First API call URL : $(APP_ADDRESS)first-api-call"
