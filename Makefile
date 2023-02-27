APPS_DIR=samples/start-app
PHP_SYMFONY_DIR=$(APPS_DIR)/php-symfony/
JS_NODE_EXPRESS_DIR=$(APPS_DIR)/js-node-express/
PYTHON_FASTAPI_DIR=$(APPS_DIR)/python-fastapi/
BIN_INSTALL_DIR=common/bin/install/

install: ## Install and start project
	@$(BIN_INSTALL_DIR)install.sh

php-install: ## Install PHP sample App
	@echo "Using PHP install script"
	cd $(PHP_SYMFONY_DIR) && make install

node-install: ## Install NodeJS sample App
	@echo "Using NodeJs install script"
	cd $(JS_NODE_EXPRESS_DIR) && make install

python-install: ## Install NodeJS sample App
	@echo "Using NodeJs install script"
	cd $(PYTHON_FASTAPI_DIR) && make install
