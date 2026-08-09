#!/bin/bash
# ============================================================
# deploy.sh — Deploy Kumaw Dimsum ke server-gratis
# Jalankan: bash deploy.sh
# ============================================================

set -e

BLUE='\033[0;34m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log() { echo -e "${BLUE}[DEPLOY]${NC} $1"; }
ok()  { echo -e "${GREEN}[OK]${NC} $1"; }
warn(){ echo -e "${YELLOW}[WARN]${NC} $1"; }
err() { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

APP_DIR="$(pwd)"

log "========================================"
log "  Kumaw Dimsum — Production Deployment"
log "========================================"

# 1. Stop OpenShip (bebaskan RAM)
log "Menghentikan OpenShip untuk membebaskan RAM..."
systemctl --user stop openship 2>/dev/null && ok "OpenShip dihentikan" || warn "OpenShip sudah tidak berjalan"

# 2. Pastikan Docker tersedia
if ! command -v docker &>/dev/null; then
    err "Docker tidak ditemukan. Install dulu: curl -fsSL https://get.docker.com | sh"
fi
ok "Docker tersedia: $(docker --version)"

# 3. Pastikan directory project ada
if [ ! -d "$APP_DIR" ]; then
    err "Directory $APP_DIR tidak ditemukan!"
fi
ok "Project ditemukan di $APP_DIR"

cd "$APP_DIR"

# 4. Stop container lama jika ada
log "Menghentikan container lama..."
docker compose -f docker-compose.prod.yml down --remove-orphans 2>/dev/null || true

# 5. Build image production
log "Building Docker image (ini mungkin butuh 5-10 menit)..."
docker compose -f docker-compose.prod.yml build --no-cache app
ok "Build selesai"

# 6. Jalankan semua service
log "Menjalankan semua service..."
docker compose -f docker-compose.prod.yml up -d
ok "Service berjalan"

# 7. Tunggu service siap
log "Menunggu service siap..."
sleep 10

# 8. Jalankan migration
log "Menjalankan database migration..."
docker exec kumaw_app php artisan migrate --force
ok "Migration selesai"

# 9. Optimasi Laravel
log "Optimasi Laravel untuk production..."
docker exec kumaw_app php artisan config:cache
docker exec kumaw_app php artisan route:cache
docker exec kumaw_app php artisan view:cache
docker exec kumaw_app php artisan storage:link
ok "Optimasi selesai"

# 10. Status akhir
echo ""
log "========================================"
ok "  DEPLOYMENT SELESAI!"
log "========================================"
echo ""
echo -e "  ${GREEN}🌐 Aplikasi:${NC}  http://104.155.140.141"
echo -e "  ${GREEN}🔌 WebSocket:${NC} ws://104.155.140.141:8080"
echo ""
docker compose -f docker-compose.prod.yml ps
