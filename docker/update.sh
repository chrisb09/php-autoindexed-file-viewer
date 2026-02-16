#!/bin/bash

# check if the script is run as root
if [ "$(id -u)" -ne 0 ]; then
  echo "Error: This script must be run as root (sudo bash update.sh)"
  exit 1
fi

# check if current directory is the docker directory
# by comparing the script's directory with the current working directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"
if [ "$SCRIPT_DIR" != "$(pwd)" ]; then
  echo "Error: This script must be run from the docker directory (cd docker && sudo bash update.sh)"
  echo "Current directory: $(pwd)"
  echo "Script directory: $SCRIPT_DIR"
  exit 1
fi

./build-image.sh


./push.sh