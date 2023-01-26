#!/bin/bash

# This script creates a .env.local file in your project's root folder, with the mandatory environment variables
# We will ask the user if he wants to erase his existing file, if it exists or just add the mandatory environment variables
#
# Requirements checked:
#   * Docker is correctly installed
#   * Docker compose is correctly installed
#
# Usage:
#  $ ./dotEnvFileCreator.sh
#

source $( dirname -- "${BASH_SOURCE[0]}" )/terminalColorsDefinition.sh

DIR="$(pwd)/"
ENV_FILE=$DIR".env"
ENV_LOCAL_FILE=$DIR".env.local"
PARENT_DIR=$(builtin cd $DIR; pwd)
CHECK_ERASE="yes"
CHECK_WRITE="no"

echo -e $(printf "${INTRO}Welcome to .env file creation script${ENDCOLOR}")

if [ -f "$ENV_LOCAL_FILE" ]; then
  echo -e $(printf "${WARNING}$(basename -- "$ENV_LOCAL_FILE") file has been found in $PARENT_DIR${ENDCOLOR}")
  read -p "Do you want to erase and rewrite this file ? (yes) : " CHECK_ERASE

  if [ "$CHECK_ERASE" != "yes" ] && [ "$CHECK_ERASE" != "y" ]; then
    echo -e $(printf "${NOTE}Make sure that CLIENT_ID and CLIENT_SECRET are included in this file${ENDCOLOR}")
    read -p "If those variables are not included, do you want to add them through this script ? (yes) : " CHECK_WRITE

    if [ "$CHECK_WRITE" != "yes" ] && [ "$CHECK_WRITE" != "y" ]; then
      echo -e $(printf "${WARNING}No change applied to $PARENT_DIR/$(basename -- "$ENV_LOCAL_FILE")${ENDCOLOR}")
      exit 0
    fi
  else
    echo -e $(printf "${NOTE}Note that this script will erase any .env.local file already existing in $PARENT_DIR${ENDCOLOR}")
    sleep 5
  fi
fi

echo -e $(printf "${NOTE}Please be sure to enter proper AKENEO's CLIENT_ID and CLIENT_SECRET : ${ENDCOLOR}")
read -p "Client id : " CLIENT_ID
read -p "Client secret : " CLIENT_SECRET
read -p "PIM url : " PIM_URL

if [[ ($CHECK_ERASE == "yes" || $CHECK_ERASE == "y") && ("$CHECK_WRITE" != "yes" && "$CHECK_WRITE" != "y") ]]; then
  cat $ENV_FILE > $ENV_LOCAL_FILE
fi

printf "\n###> Akeneo's OAuth2 environment variables ###\n" >> $ENV_LOCAL_FILE
printf "CLIENT_ID=%s\n" $CLIENT_ID >> $ENV_LOCAL_FILE
printf "CLIENT_SECRET=%s\n" $CLIENT_SECRET >> $ENV_LOCAL_FILE
printf "PIM_URL=%s\n" $PIM_URL >> $ENV_LOCAL_FILE
printf "###< Akeneo's OAauth2 environment variables ###" >> $ENV_LOCAL_FILE

echo -e $(printf "${SUCCESS}.env file has been written to $PARENT_DIR/$(basename -- "$ENV_LOCAL_FILE")${ENDCOLOR}")