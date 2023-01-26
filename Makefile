APPS_DIR=samples/start-app
PHP_SYMFONY_DIR=$(APPS_DIR)/php-symfony/
BIN_INSTALL_DIR=bin/install/

install: ## Install and start project
	@bin/install/install.sh

php-install: ## Install PHP sample App
	@echo "Using PHP install script"
	cd $(PHP_SYMFONY_DIR) && make install
