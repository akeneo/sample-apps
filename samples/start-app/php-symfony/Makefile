DOCKER_COMPOSE = docker compose
DOCKER_BUILD = $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down

install: ## Install PHP sample App
	@$(MAKE) requirements
	@$(MAKE) post-requirements

requirements: ## build docker image
	@bin/install/checkRequirements.sh
	@bin/install/dotEnvFileCreator.sh

post-requirements: build
	@$(MAKE) start

build: ## build docker image
	$(DOCKER_BUILD)

start:
	$(DOCKER_UP)

stop:
	$(DOCKER_DOWN)