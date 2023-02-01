#!/bin/bash

# Lets the user choose its preferred language when running make install
#
# Usage:
#  $ ./install.sh
#

source samples/start-app/php-symfony/bin/install/terminalColorsDefinition.sh

echo -e $(printf "${INTRO}Welcome to Akeneo's Sample Apps intallation process :${ENDCOLOR}")
echo "1 - Use make php-install for PHP / Symfony sample app"
echo "2 - Use make php-node for NodeJS sample app"
echo "3 - Use make php-python for Python sample app"
echo "0 - To quit installation"
read -p "Your choice : (0) " USER_CHOICE

case $USER_CHOICE in
    1)
        make php-install
        ;;
    2)
        echo -e $(printf "${WARNING}NodeJS not implemented yet${ENDCOLOR}")
        ;;
    3)
        echo -e $(printf "${WARNING}Python not implemented yet${ENDCOLOR}")
        ;;
    *)
        echo -e $(printf "${WARNING}Installation script ended${ENDCOLOR}")
        ;;
esac
