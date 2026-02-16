#!/bin/bash

# Extract the version from index.php
VERSION=$(grep -oP "define\('APP_VERSION',\s*'\K[^']+" ../index.php)

echo "Tagging Docker image with version: $VERSION"

read -p "Is the version correct? (y/n): " CONFIRM
if [[ "$CONFIRM" != "y" ]]; then
    echo "Aborting."
    exit 1
fi

# Check if docker is installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed. Please install Docker and try again."
    exit 1
fi

# Check if dockerhub already has an image with the same version
if docker pull chrisb09/php-autoindexed-file-viewer:$VERSION &> /dev/null; then
    echo "Error: An image with version $VERSION already exists on Docker Hub. Please update the version in index.php before pushing."
    exit 1
fi

docker login
docker tag php-autoindexed-file-viewer:latest chrisb09/php-autoindexed-file-viewer:$VERSION
docker push chrisb09/php-autoindexed-file-viewer:$VERSION
docker tag php-autoindexed-file-viewer:latest chrisb09/php-autoindexed-file-viewer:latest
docker push chrisb09/php-autoindexed-file-viewer:latest