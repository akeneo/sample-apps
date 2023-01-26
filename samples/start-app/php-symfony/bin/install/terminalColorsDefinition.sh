#!/bin/bash

# Defines the UI of Akeneo's Sample Apps installation scripts
#
# Usage in a script:
#  source <path to this script>/terminalColorsDefinition.sh
#

GREEN="32"
YELLOW="33"
BLUE="34"
MAGENTA_BG="45"
INTRO="\e[1;${MAGENTA_BG}m"
NOTE="\e[${BLUE}m"
WARNING="\e[1;${YELLOW}m"
SUCCESS="\e[1;${GREEN}m"
ENDCOLOR="\e[0m"
