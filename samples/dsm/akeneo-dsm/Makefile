BACK_NAME = back
FRONT_NAME = front
export DEV_UID = $(shell id -u)
export DEV_GID = $(shell id -g)
DOCKER_COMPOSE = docker compose
DOCKER_BUILD = BUILDKIT=1 $(DOCKER_COMPOSE) build
DOCKER_UP = $(DOCKER_COMPOSE) up -d
DOCKER_DOWN = $(DOCKER_COMPOSE) down
DOCKER_EXEC = $(DOCKER_COMPOSE) run
LOG_FILE = ./back-end/var/error.log
APP_ADDRESS = http://localhost:3000/

install: ## Install DSM App
	@$(MAKE) requirements
	@$(MAKE) post-requirements
	@$(MAKE) _display

requirements: ## Check all requirements before building and starting the App
	@../../../common/bin/install/checkRequirements.sh
	@../../../common/bin/install/dotEnvFileCreator.sh local back-end

post-requirements: build
	@$(MAKE) start
	@echo "Installing dependencies"
	@$(DOCKER_EXEC) $(BACK_NAME) npm install
	@echo "Installing SQLite migrations"
	@$(DOCKER_EXEC) $(BACK_NAME) npx prisma migrate dev
	@$(MAKE) front

build: ## build docker image
	$(DOCKER_BUILD)

start: ## start docker image
	$(DOCKER_UP)
	@$(MAKE) _display

stop: ## stop docker image
	$(DOCKER_DOWN)

logs: ## displays logs
	tail -f $(LOG_FILE)

.PHONY: all tests clean
tests:
	@../../../common/bin/install/dotEnvFileCreator.sh test back-end
	@$(DOCKER_EXEC) $(BACK_NAME) npm test

_display: ##
	@echo ""
	@echo "**** ReactJS + NodeJS ****"
	@echo ""
	@echo "     Web app (served by ReactJS + NodeJS): $(APP_ADDRESS)"

front:
	@$(DOCKER_EXEC) $(FRONT_NAME) npm install
