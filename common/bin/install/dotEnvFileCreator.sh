#!/bin/bash

# This script creates a .env.local file in your project's root folder, with the mandatory environment variables
# We will ask the user if he wants to erase his existing file, if it exists or just add the mandatory environment variables
#
# Usage:
#  $ ./dotEnvFileCreator.sh
#

source $(dirname -- "${BASH_SOURCE[0]}")/terminalColorsDefinition.sh

DIR="$(pwd)"
ENV_FILE="$DIR"/.env
ENV_LOCAL_FILE="$DIR"/.env.local
ENV_TEST_FILE="$DIR"/.env.test
PARENT_DIR=$(builtin cd "$DIR"; pwd)
CHECK_ERASE="yes"
CHECK_WRITE="no"
DOCKER_VERSION="$(docker -v | cut -d ' ' -f3 | cut -d',' -f1)"

FillLocalValues() {
  echo -e $(printf "${INTRO}Welcome to .env file creation script${ENDCOLOR}")

    if [ -f "$ENV_LOCAL_FILE" ]; then
      echo -e $(printf "${WARNING}$(basename -- "$ENV_LOCAL_FILE") file has been found in $PARENT_DIR${ENDCOLOR}")
      read -p "Do you want to erase and rewrite this file ? (yes) : " CHECK_ERASE

      if [ "$CHECK_ERASE" != "yes" ] && [ "$CHECK_ERASE" != "y" ]; then
        echo -e $(printf "${NOTE}Make sure that CLIENT_ID, CLIENT_SECRET and AKENEO_PIM_URL are included in this file${ENDCOLOR}")
        read -p "If those variables are not included, do you want to add them through this script ? (yes) : " CHECK_WRITE

        if [ "$CHECK_WRITE" != "yes" ] && [ "$CHECK_WRITE" != "y" ]; then
          echo -e $(printf "${WARNING}No change applied to $PARENT_DIR/$(basename -- "$ENV_LOCAL_FILE")${ENDCOLOR}")
          return 0
        fi
      else
        echo -e $(printf "${NOTE}Note that this script will erase any .env.local file already existing in $PARENT_DIR${ENDCOLOR}")
        sleep 5
      fi
    fi

    echo -e $(printf "${NOTE}Please be sure to enter proper AKENEO's CLIENT_ID, CLIENT_SECRET and AKENEO_PIM_URL : ${ENDCOLOR}")
    read -p "Client id : " CLIENT_ID
    read -p "Client secret : " CLIENT_SECRET
    read -p "PIM url : " PIM_URL

    return 1
}

FillTestValues() {
  if [ -f "$ENV_TEST_FILE" ]; then
    return 0
  fi
  CLIENT_ID=test_client_id
  CLIENT_SECRET=test_client_secret
  PIM_URL=http://a_random_pim_url.com

  return 1
}

CreateDotEnvFile() {
  FILE=$1
  if [[ ($CHECK_ERASE == "yes" || $CHECK_ERASE == "y") && ("$CHECK_WRITE" != "yes" && "$CHECK_WRITE" != "y") ]]; then
    cat "$ENV_FILE" >"$FILE"
  fi

  printf "\n###> Akeneo's OAuth2 environment variables ###\n" >>"$FILE"
  printf "CLIENT_ID=%s\n" $CLIENT_ID >>"$FILE"
  printf "CLIENT_SECRET=%s\n" $CLIENT_SECRET >>"$FILE"
  printf "AKENEO_PIM_URL=%s\n" $PIM_URL >>"$FILE"
  printf "###< Akeneo's OAauth2 environment variables ###\n" >>"$FILE"

  printf "\n###> Docker version ###\n" >>"$FILE"
  printf "DOCKER_VERSION=%s\n" $DOCKER_VERSION >>"$FILE"
  printf "###< Docker version ###\n" >>"$FILE"
}

if [ $# -eq 0 ] || [ $1 = "local" ]; then
  FillLocalValues
  if [ $? != 0 ]; then
    CreateDotEnvFile $ENV_LOCAL_FILE
    echo -e $(printf "${SUCCESS}.env file has been written to $PARENT_DIR/$(basename -- "$FILE")${ENDCOLOR}")
  fi
elif [ $1 = "test" ]; then
  FillTestValues
    if [ $? != 0 ]; then
      CreateDotEnvFile $ENV_TEST_FILE
    fi
else
  echo -e $(printf "${WARNING}Unknown error. Please use this script with no argument or with the following ones : ${ENDCOLOR}")
  echo -e $(printf "${WARNING}\t - local : (default) to create a .env.local file ${ENDCOLOR}")
  echo -e $(printf "${WARNING}\t - test : to create a .env.test file ${ENDCOLOR}")
fi
