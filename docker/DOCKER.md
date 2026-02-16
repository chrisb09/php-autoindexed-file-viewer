# Docker Deployment Guide

## Quick Start

1. **Build the Docker image:**
   ```bash
   bash build-image.sh
   ```

2. **Run with docker-compose:**
   ```bash
   cd docker
   docker-compose up -d
   ```

3. **Access the application:**
   - Open `http://localhost:7480` in your browser

4. **Add files:**
   - Place files in the `../files` directory (project root)
   - They'll be immediately available in the web interface

## Files

- **Dockerfile** — Multi-stage PHP 8.2-FPM image with all dependencies
- **docker-compose.yml** — Orchestrates PHP-FPM + nginx
- **nginx.conf** — nginx configuration (fastcgi routing to PHP-FPM)

## Volumes

- `../files` — Serves your actual file content (mapped to `/app/files` in container)
- `file_browser_cache` — Persistent Docker volume for thumbnails and metadata cache

## Configuration

Edit `docker-compose.yml` environment variables to customize:

| Variable | Default | Description |
|----------|---------|-------------|
| `PHP_MEMORY_LIMIT` | `512M` | PHP memory limit |
| `PHP_UPLOAD_MAX_FILESIZE` | `10G` | Max file upload size |
| `PHP_POST_MAX_SIZE` | `10G` | Max POST request size |

## Production Deployment

### HTTPS with Let's Encrypt

1. Update `docker-compose.yml` port mappings:
   ```yaml
   ports:
     - "80:80"
     - "443:443"
   ```

2. Use a reverse proxy (Traefik, Caddy) or update `nginx.conf` with SSL certificates.

### Scaling

For better performance with many concurrent users:

```yaml
  app:
    deploy:
      replicas: 2
```

Use a load balancer in front of multiple PHP-FPM instances.

## Useful Commands

```bash
# View logs
docker-compose logs -f app

# Rebuild and restart
docker-compose up

# Stop containers
docker-compose down

# Clean up everything (including volumes)
docker-compose down -v

# Access container shell
docker-compose exec app sh
```

## System Requirements

- Docker 20.10+
- Docker Compose 2.0+
- ~500MB disk space (without files)

## Troubleshooting

**Permission denied errors:**
- Ensure `../files` directory exists and is writable: `mkdir -p ../files && chmod 755 ../files`

**Slow thumbnails:**
- Check CPU/memory allocation to Docker
- Monitor with: `docker stats`

**Large files timeout:**
- Increase timeouts in `nginx.conf` (already generous at 600s)
- Increase PHP `max_execution_time` in `docker-compose.yml`
