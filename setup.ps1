# Kumaw Dimsum — Quick Setup Script (PowerShell)
# Run from project root: .\setup.ps1
# Prerequisites: Docker Desktop running, Git installed

Write-Host "=== Kumaw Dimsum — Project Setup ===" -ForegroundColor Cyan

# Step 1: Install Laravel 11
Write-Host "`n[1/6] Installing Laravel 11..." -ForegroundColor Yellow
composer create-project laravel/laravel:^11.0 . --prefer-dist

# Step 2: Install Reverb
Write-Host "`n[2/6] Installing Laravel Reverb..." -ForegroundColor Yellow
php artisan install:broadcasting

# Step 3: Copy .env
Write-Host "`n[3/6] Creating .env from template..." -ForegroundColor Yellow
Copy-Item .env.example .env

# Step 4: Build Docker images
Write-Host "`n[4/6] Building Docker containers..." -ForegroundColor Yellow
docker compose build --no-cache

# Step 5: Start containers
Write-Host "`n[5/6] Starting containers..." -ForegroundColor Yellow
docker compose up -d

# Wait for MySQL to be healthy
Write-Host "Waiting for MySQL to be ready..." -ForegroundColor Gray
Start-Sleep -Seconds 15

# Step 6: Run migrations inside app container
Write-Host "`n[6/6] Generating app key & running migrations..." -ForegroundColor Yellow
docker exec kumaw_app php artisan key:generate
docker exec kumaw_app php artisan migrate --force

Write-Host "`n=== Setup Complete! ===" -ForegroundColor Green
Write-Host "App:      http://localhost:8000" -ForegroundColor White
Write-Host "WebSocket: ws://localhost:8080" -ForegroundColor White
Write-Host "MySQL:    localhost:3306 (use SQLyog)" -ForegroundColor White
