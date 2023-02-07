#!/bin/bash

# Lets the user choose its preferred language when running make install
#
# Usage:
#  $ ./install.sh
#

source samples/start-app/php-symfony/bin/install/terminalColorsDefinition.sh

echo -e $(printf "${INTRO}Welcome to Akeneo's Sample Apps intallation process :${ENDCOLOR}")
echo "1 - Use make php-install for PHP / Symfony sample app"
echo "2 - Use make node-install for NodeJS sample app"
echo "3 - Use make python-install for Python sample app"
echo "0 - To quit installation"
read -p "Your choice : (0) " USER_CHOICE

case $USER_CHOICE in
    1)
        make php-install
        ;;
    2)
        make node-install
        ;;
    3)
        make python-install
        ;;
    *)
        echo -e $(printf "${WARNING}Installation script ended${ENDCOLOR}")
        ;;
esac
