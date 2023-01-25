APPS_DIR=samples/start-app
PHP_SYMFONY_DIR=$(APPS_DIR)/php-symfony/
BIN_INSTALL_DIR=bin/install/

install: ## Install and start project
	@echo "Welcome to Akeneo's sample-apps intallation process :"
	@echo "Use make php-install for PHP / Symfony sample app"
	@echo "Use make php-node for NodeJS sample app"
	@echo "Use make php-python for Python sample app"

php-install: ## Install PHP sample App
	@echo "Using PHP install script"
	cd $(PHP_SYMFONY_DIR) && make install