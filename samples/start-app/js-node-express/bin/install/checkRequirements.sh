#!/bin/bash

# This script checks that every requirements to build and start a sample app is filled
#
# Requirements checked:
#   * Docker is correctly installed
#   * Docker compose is correctly installed
#
# Usage:
#  $ ./checkRequirements.sh
#

source $( dirname -- "${BASH_SOURCE[0]}" )/terminalColorsDefinition.sh

if ! [ -x "$(command -v docker)" ]; then
    echo -e $(printf "${WARNING}Docker is mandatory in order to run your App.${ENDCOLOR}")
    echo -e $(printf "${WARNING}Please install the latest version of Docker on your computer before running this script.${ENDCOLOR}")
    echo -e $(printf "${WARNING}Install documentation : https://docs.docker.com/engine/install/${ENDCOLOR}")
    exit 1
fi

docker compose version > /dev/null 2>&1
DOCKER_COMPOSE_STATUS=$?

if ! [ $DOCKER_COMPOSE_STATUS -eq 0 ]; then
    echo -e $(printf "${WARNING}Docker Compose is mandatory in order to run your apps.${ENDCOLOR}")
    echo -e $(printf "${WARNING}Please install the latest version of docker compose on your computer before running this script.${ENDCOLOR}")
    echo -e $(printf "${WARNING}Install documentation : https://docs.docker.com/compose/install/${ENDCOLOR}")
    exit 1
fi

SERVER_VERSION=$(docker compose version --short)
SERVER_VERSION_MAJOR=$(echo "$SERVER_VERSION"| cut -d'.' -f 1)

if [ "${SERVER_VERSION_MAJOR}" -eq 1 ]; then
    echo -e $(printf "${WARNING}Docker Compose V1 is deprecated, you should use Docker Compose V2 (your current version is ${SERVER_VERSION})${ENDCOLOR}.")
    exit 1
fi

echo -e $(printf "${SUCCESS}All requirements are correctly filled${ENDCOLOR}")
exit 0
