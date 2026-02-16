#!/usr/bin/env bash
set -euo pipefail

# Build Docker image for PHP Autoindexed File Viewer
# Usage: bash build-image.sh

IMAGE_NAME="chrisb09/php-autoindexed-file-viewer"
DOCKERFILE="Dockerfile"
CONTEXT=".."

echo "Building Docker image: $IMAGE_NAME"
docker build -f "$DOCKERFILE" -t "$IMAGE_NAME:latest" "$CONTEXT"

echo ""
echo "✓ Image built successfully!"
echo ""
echo "You can now use the image in docker-compose.yml:"
echo "  image: $IMAGE_NAME:latest"
