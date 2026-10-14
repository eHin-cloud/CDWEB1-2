$ErrorActionPreference = "Stop"

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
docker compose -f "$scriptDir\docker-compose.yml" up --build
