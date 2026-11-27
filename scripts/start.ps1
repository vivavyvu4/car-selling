$ErrorActionPreference = "Stop"
Set-Location (Split-Path -Parent $PSScriptRoot)
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw "Docker Desktop is required: https://www.docker.com/products/docker-desktop/"
}
docker compose version | Out-Null
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Created .env from .env.example. Review local credentials before sharing this machine."
}
docker compose up -d
Write-Host ""
Write-Host "First-time setup logs: docker compose logs -f setup"
Write-Host "Website: http://localhost:8080"
Write-Host "Admin:   http://localhost:8080/wp-admin"
