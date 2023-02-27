APP_NAME = app
UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_COMPOSE = docker-compose
DOCKER_BUILD = $(DOCKER_COMPOSE) --user "$(UID):$(GID)" build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
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

_display: ##
	@echo ""
	@echo "**** Python + FastApi Sample App ****"
	@echo ""
	@echo "     Web app (served by Python + FastApi with hot reload): $(APP_ADDRESS)"
	@echo "     Activate URL : $(APP_ADDRESS)activate"
	@echo "     Callback URL : $(APP_ADDRESS)callback"
	@echo "     First API call URL : $(APP_ADDRESS)first-api-call"
