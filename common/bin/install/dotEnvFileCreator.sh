#!/bin/bash

# This script creates a .env.local file in your project's root folder, with the mandatory environment variables
# We will ask the user if he wants to erase his existing file, if it exists or just add the mandatory environment variables
#
# Usage:
#  $ ./dotEnvFileCreator.sh [?env]
#      - env : possible values are empty, "local" and "test"
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
EXISTING_VARIABLES=()

## Create the file from .env file
# @param FILE : target file
# @param FORCE : force the creation even though the file already exists
InitFile() {
  FILE=$1
  FORCE=$2
  if [ ! -f "$FILE" ] || [ ! -z $FORCE ]; then
    cat "$ENV_FILE" > "$FILE"
  else
    echo -e $(printf "${WARNING}$(basename -- "$ENV_LOCAL_FILE") file has been found in $PARENT_DIR${ENDCOLOR}")
  fi
}

## Check that values exists in given file
# @param FILE : target file
CheckValues() {
  FILE=$1
  if grep -q "OPENID_AUTHENTICATION" "$FILE"; then
    EXISTING_VARIABLES+=("OPENID_AUTHENTICATION")
  fi
  if grep -q "CLIENT_ID" "$FILE"; then
    EXISTING_VARIABLES+=("CLIENT_ID")
  fi
  if grep -q "CLIENT_SECRET" "$FILE"; then
    EXISTING_VARIABLES+=("CLIENT_SECRET")
  fi
  if grep -q "PIM_URL" "$FILE"; then
    EXISTING_VARIABLES+=("PIM_URL")
  fi
  if grep -q "DOCKER_VERSION" "$FILE"; then
    EXISTING_VARIABLES+=("DOCKER_VERSION")
  fi
  if grep -q "SUB_HASH_KEY" "$FILE"; then
    EXISTING_VARIABLES+=("SUB_HASH_KEY")
  fi
}

## Input from user for authentication information, when at least one of them is missing
ReadLocalAuthentication() {
  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "CLIENT_ID" || ! "${EXISTING_VARIABLES[*]}" =~ "CLIENT_SECRET" || ! "${EXISTING_VARIABLES[*]}" =~ "PIM_URL" ]]; then
    echo -e $(printf "${NOTE}Please be sure to enter proper AKENEO's CLIENT_ID, CLIENT_SECRET and AKENEO_PIM_URL : ${ENDCOLOR}")
    read -p "Client id : " CLIENT_ID
    read -p "Client secret : " CLIENT_SECRET
    read -p "PIM url : " PIM_URL
  fi
}

## Input from user for OpenID Connect authentication
ReadLocalOpenId() {
  if [[ ! " ${EXISTING_VARIABLES[*]} " =~ "OPENID_AUTHENTICATION" ]]; then
    echo -e $(printf "${NOTE}Apps created in the Akeneo App Store can use the OpenID Connect protocol to authenticate users coming from an Akeneo PXM Studio. ${ENDCOLOR}")
    echo -e $(printf "${NOTE}Akeneo's documentation about OpenID Connect authentication : https://api.akeneo.com/apps/authentication-and-authorization.html#getting-started-with-openid-connect${ENDCOLOR}")
    read -p "Would you like to activate OpenID Connect authentication ? (yes) : " OPENID_AUTHENTICATION_USAGE
    OPENID_AUTHENTICATION=1
    RANDOM_KEY=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 15 ; echo '')
    if [[ "$OPENID_AUTHENTICATION_USAGE" != "yes" && "$OPENID_AUTHENTICATION_USAGE" != "y" ]] && [ ! -z $OPENID_AUTHENTICATION_USAGE ]; then
      OPENID_AUTHENTICATION=0
    fi
  fi
}

## Checks values, read user inputs and fills the different missing values. Supposedly covers every use case
CheckAndFillLocalValues() {
  CheckValues "$ENV_LOCAL_FILE"

  if (( ${#EXISTING_VARIABLES[@]} )); then
    # File already exists with at least one needed variable
    read -p "Do you want to erase and rewrite this file ? (yes) : " CHECK_ERASE

    if [ "$CHECK_ERASE" != "yes" ] && [ "$CHECK_ERASE" != "y" ] && [ ! -z $CHECK_ERASE ]; then
      # User wants to keep file
      echo -e $(printf "${NOTE}Make sure that CLIENT_ID, CLIENT_SECRET and AKENEO_PIM_URL are included in this file${ENDCOLOR}")
      read -p "Some variables may not be included, do you want to add them through this script ? (yes) : " CHECK_WRITE

      if [ "$CHECK_WRITE" != "yes" ] && [ "$CHECK_WRITE" != "y" ] && [ ! -z $CHECK_WRITE ]; then
        # User does not want to add possible missing information to its file
        echo -e $(printf "${WARNING}No change applied to $PARENT_DIR/$(basename -- "$ENV_LOCAL_FILE")${ENDCOLOR}")
        return 0
      fi
    else
      # User wants to erase file
      echo -e $(printf "${NOTE}Note that this script will erase any .env.local file already existing in $PARENT_DIR${ENDCOLOR}")
      sleep 5
      InitFile "$ENV_LOCAL_FILE" 1
      CheckValues "$ENV_LOCAL_FILE"
    fi
  fi

  ReadLocalAuthentication
  ReadLocalOpenId

  return 1
}

## Checks and and fills the different testing values if they are missing
CheckAndFillTestValues() {
  CheckValues "$ENV_TEST_FILE"

  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "CLIENT_ID" ]]; then
    CLIENT_ID=test_client_id
  fi
  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "CLIENT_SECRET" ]]; then
    CLIENT_SECRET=test_client_secret
  fi
  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "PIM_URL" ]]; then
    PIM_URL=http://a_random_pim_url.com
  fi
  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "OPENID_AUTHENTICATION" ]]; then
    OPENID_AUTHENTICATION=1
  fi
  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "SUB_HASH_KEY" ]]; then
    RANDOM_KEY=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 15 ; echo '')
  fi

  return 1
}

## Write the target file with every missing information collected
WriteDotEnvFile() {
  FILE=$1

  if [ ! -z ${OPENID_AUTHENTICATION} ]; then
    printf "\n###> Use of OpenID Connect protocol to authenticate from a PXM ###\n" >>"$FILE"
    printf "### https://api.akeneo.com/apps/authentication-and-authorization.html#getting-started-with-openid-connect\n" >>"$FILE"
    printf "OPENID_AUTHENTICATION=%s\n" $OPENID_AUTHENTICATION >>"$FILE"
    printf "SUB_HASH_KEY=%s\n" $RANDOM_KEY >>"$FILE"
    printf "###< Use of OpenID Connect protocol to authenticate from a PXM ###\n" >>"$FILE"
  fi

  if [ ! -z ${CLIENT_ID} ] || [ ! -z ${CLIENT_SECRET} ] || [ ! -z ${PIM_URL} ]; then
    printf "\n###> Akeneo's OAuth2 environment variables ###\n" >>"$FILE"
    printf "CLIENT_ID=%s\n" $CLIENT_ID >>"$FILE"
    printf "CLIENT_SECRET=%s\n" $CLIENT_SECRET >>"$FILE"
    printf "PIM_URL=%s\n" $PIM_URL >>"$FILE"
    printf "###< Akeneo's OAauth2 environment variables ###\n" >>"$FILE"
  fi

  if [[ ! "${EXISTING_VARIABLES[*]}" =~ "DOCKER_VERSION" ]]; then
    printf "\n###> Docker version ###\n" >>"$FILE"
    printf "DOCKER_VERSION=%s\n" $DOCKER_VERSION >>"$FILE"
    printf "###< Docker version ###\n" >>"$FILE"
  fi
}

## Main script
if [ $# -eq 0 ] || [ $1 = "local" ]; then
  echo -e $(printf "${INTRO}Welcome to .env file creation script${ENDCOLOR}")
  InitFile "$ENV_LOCAL_FILE"
  CheckAndFillLocalValues
  if [ $? != 0 ]; then
    WriteDotEnvFile "$ENV_LOCAL_FILE"
    echo -e $(printf "${SUCCESS}.env file has been written to $PARENT_DIR/$(basename -- "$FILE")${ENDCOLOR}")
  fi
elif [ $1 = "test" ]; then
  InitFile "$ENV_TEST_FILE"
  CheckAndFillTestValues
    if [ $? != 0 ]; then
      WriteDotEnvFile "$ENV_TEST_FILE"
    fi
else
  echo -e $(printf "${WARNING}Unknown error. Please use this script with no argument or with the following ones : ${ENDCOLOR}")
  echo -e $(printf "${WARNING}\t - local : (default) to create a .env.local file ${ENDCOLOR}")
  echo -e $(printf "${WARNING}\t - test : to create a .env.test file ${ENDCOLOR}")
fi
