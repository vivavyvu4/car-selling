#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
if ! command -v docker >/dev/null 2>&1; then
  echo "Docker Desktop is required: https://www.docker.com/products/docker-desktop/"
  exit 1
fi
if ! docker compose version >/dev/null 2>&1; then
  echo "Docker Compose v2 is required."
  exit 1
fi
if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env from .env.example. Review local credentials before sharing this machine."
fi
docker compose up -d
echo ""
echo "First-time setup logs: docker compose logs -f setup"
echo "Website: http://localhost:8080"
echo "Admin:   http://localhost:8080/wp-admin"
