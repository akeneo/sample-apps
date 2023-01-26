DOCKER_COMPOSE = docker compose
DOCKER_BUILD = $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
APP_ADDRESS = http://localhost:8082/

install: ## Install PHP sample App
	@$(MAKE) requirements
	@$(MAKE) post-requirements

requirements: ## Check all requirements before building and starting the App
	@bin/install/checkRequirements.sh
	@bin/install/dotEnvFileCreator.sh

post-requirements: build
	@$(MAKE) start

build: ## build docker image
	$(DOCKER_BUILD)

start: ## start docker image
	$(DOCKER_UP)
	@echo "Running on $(APP_ADDRESS)"

stop: ## stop docker image
	$(DOCKER_DOWN)
