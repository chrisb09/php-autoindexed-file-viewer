#!/usr/bin/env bash
set -euo pipefail

# ============================================================
# install.sh — dependency setup for the PHP File Browser
# Tested on Debian 12 / Ubuntu 22.04+ with PHP 8.2
# Run as root: sudo bash install.sh
# ============================================================

if [ "$(id -u)" -ne 0 ]; then
  echo "Error: This script must be run as root (sudo bash install.sh)"
  exit 1
fi

echo "==> Installing PHP extensions..."
apt-get update -qq
apt-get install -y \
  php8.2-gd \
  php8.2-mbstring \
  php8.2-zip \
  php8.2-sqlite3 \
  php8.2-xml

echo "==> Installing system tools..."
apt-get install -y \
  mediainfo \
  ffmpeg \
  poppler-utils \
  imagemagick

echo "==> Creating cache directory..."
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
mkdir -p "$SCRIPT_DIR/.cache_fb"
chown -R www-data:www-data "$SCRIPT_DIR/.cache_fb"
chmod 755 "$SCRIPT_DIR/.cache_fb"

echo "==> Creating files directory..."
mkdir -p "$SCRIPT_DIR/files"
chown -R www-data:www-data "$SCRIPT_DIR/files"

echo "==> Restarting PHP-FPM..."
systemctl restart php8.2-fpm 2>/dev/null || echo "    (php8.2-fpm not found, restart manually if needed)"

echo ""
echo "Done. Point your web server's document root to:"
echo "  $SCRIPT_DIR"
echo ""
echo "Make sure your nginx/apache config passes .php files to PHP-FPM."