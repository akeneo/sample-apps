APP_NAME = app
export DEV_UID = $(shell id -u)
export DEV_GID = $(shell id -g)
DOCKER_COMPOSE = docker compose
DOCKER_BUILD = BUILDKIT=1 $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run

ifneq (,$(wildcard ./.env))
    include .env
    export
    APP_ADDRESS = http://localhost:$(APP_PORT)/
endif

install: ## Install Rust sample App
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

stop: ## remove docker container
	$(DOCKER_DOWN)

tests:
	$(DOCKER_EXEC) $(APP_NAME) cargo test

bash:
	$(DOCKER_EXEC) $(APP_NAME) bash

_display: ##
	@echo ""
	@echo "**** Rust + Actix Web Sample App ****"
	@echo ""
	@echo "     Web app (served by Rust + Actix Web with hot reload): $(APP_ADDRESS)"
	@echo "     Activate URL : $(APP_ADDRESS)activate"
	@echo "     Callback URL : $(APP_ADDRESS)callback"
	@echo "     First API call URL : $(APP_ADDRESS)first-api-call"
	@echo ""
	@echo "     /!\ Docker first start can take time to build application"
