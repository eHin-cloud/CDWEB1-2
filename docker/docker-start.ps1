$ErrorActionPreference = "Stop"

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
docker compose -p cdweb1-2 -f "$scriptDir\docker-compose.yml" up --build
