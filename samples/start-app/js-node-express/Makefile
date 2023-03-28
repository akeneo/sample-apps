APP_NAME = app
export DEV_UID = $(shell id -u)
export DEV_GID = $(shell id -g)
DOCKER_COMPOSE = docker compose
DOCKER_BUILD = $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run
LOG_FILE = /srv/app/var/error.log
APP_ADDRESS = http://localhost:8081/

install: ## Install NodeJS sample App
	@$(MAKE) requirements
	@$(MAKE) post-requirements
	@$(MAKE) _display

requirements: ## Check all requirements before building and starting the App
	@../../../common/bin/install/checkRequirements.sh
	@../../../common/bin/install/dotEnvFileCreator.sh

post-requirements: build
	@$(MAKE) start
	@echo "Installing dependencies"
	@$(DOCKER_EXEC) $(APP_NAME) npm install
	@echo "Installing SQLite migrations"
	@$(DOCKER_EXEC) $(APP_NAME) npx prisma migrate dev

build: ## build docker image
	$(DOCKER_BUILD)

start: ## start docker image
	$(DOCKER_UP)
	@$(MAKE) _display

stop: ## stop docker image
	$(DOCKER_DOWN)

logs: ## displays logs
	$(DOCKER_EXEC) $(APP_NAME) tail -f $(LOG_FILE)

.PHONY: all tests clean
tests:
	@../../../common/bin/install/dotEnvFileCreator.sh test
	@$(DOCKER_EXEC) $(APP_NAME) npm test

_display: ##
	@echo ""
	@echo "**** NodeJS + Express Sample App ****"
	@echo ""
	@echo "     Web app (served by NodeJS + Express with hot reload): $(APP_ADDRESS)"
	@echo "     Activate URL : $(APP_ADDRESS)activate"
	@echo "     Callback URL : $(APP_ADDRESS)callback"
	@echo "     First API call URL : $(APP_ADDRESS)first-api-call"
