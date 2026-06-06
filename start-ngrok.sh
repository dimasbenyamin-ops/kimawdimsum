#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
#  start-ngrok.sh  — Start ngrok tunnel for Kumaw Dimsum and update APP_URL
#  Usage:  bash start-ngrok.sh
# ─────────────────────────────────────────────────────────────────────────────

NGROK_BIN="${HOME}/bin/ngrok"
ENV_FILE="$(cd "$(dirname "$0")" && pwd)/.env"
PORT=8000

# ── 1. Check ngrok binary ────────────────────────────────────────────────────
if [ ! -x "$NGROK_BIN" ]; then
    echo "❌  ngrok not found at $NGROK_BIN"
    exit 1
fi

# ── 2. Check config file exists (authtoken already saved) ────────────────────
if ! "$NGROK_BIN" config check 2>&1 | grep -qi "valid"; then
    echo ""
    echo "⚠️  ngrok config is invalid or missing."
    echo "   Run: ~/bin/ngrok config add-authtoken YOUR_TOKEN"
    echo ""
    exit 1
fi

# ── 3. Kill any existing ngrok process ───────────────────────────────────────
pkill -f "ngrok http" 2>/dev/null || true
sleep 1

# ── 4. Start ngrok in the background ─────────────────────────────────────────
echo "🚀  Starting ngrok tunnel on port $PORT..."
"$NGROK_BIN" http $PORT > /tmp/ngrok-kumaw.log 2>&1 &
NGROK_PID=$!

# ── 5. Wait for the API to become ready and get the public URL ───────────────
echo "⏳  Waiting for ngrok URL..."
URL=""
for i in $(seq 1 20); do
    sleep 1
    URL=$(curl -s http://127.0.0.1:4040/api/tunnels 2>/dev/null \
        | grep -o '"public_url":"https://[^"]*"' \
        | head -1 \
        | sed 's/"public_url":"//;s/"//')
    if [ -n "$URL" ]; then break; fi
done

if [ -z "$URL" ]; then
    echo "❌  Could not get ngrok URL after 20s."
    echo "    Check log: cat /tmp/ngrok-kumaw.log"
    cat /tmp/ngrok-kumaw.log
    kill $NGROK_PID 2>/dev/null
    exit 1
fi

# ── 6. Print the result ───────────────────────────────────────────────────────
echo ""
echo "✅  ngrok tunnel active!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   🌐  Public URL  : $URL"
echo "   📱  Open on phone: $URL"
echo "   🔧  Inspector   : http://127.0.0.1:4040"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── 7. Update .env APP_URL ────────────────────────────────────────────────────
if [ -f "$ENV_FILE" ]; then
    OLD_URL=$(grep "^APP_URL=" "$ENV_FILE" | cut -d'=' -f2-)
    sed -i "s|^APP_URL=.*|APP_URL=${URL}|" "$ENV_FILE"
    echo "📝  .env updated: APP_URL=$URL  (was: $OLD_URL)"
fi

# ── 8. Clear Laravel config cache in Docker ───────────────────────────────────
COMPOSE_FILE="$(cd "$(dirname "$0")" && pwd)/docker-compose.yml"
if [ -f "$COMPOSE_FILE" ]; then
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear 2>/dev/null \
        && echo "🔄  Laravel config cache cleared." \
        || echo "⚠️  Could not clear config cache."
fi

echo ""
echo "🎉  Open $URL on your phone now!"
echo "    Press Ctrl+C to stop ngrok and restore localhost."
echo ""

# ── 9. Restore on exit ────────────────────────────────────────────────────────
cleanup() {
    echo ""
    echo "🛑  Stopping ngrok..."
    kill $NGROK_PID 2>/dev/null
    if [ -f "$ENV_FILE" ]; then
        sed -i "s|^APP_URL=.*|APP_URL=http://localhost:8000|" "$ENV_FILE"
        echo "✅  APP_URL restored to http://localhost:8000"
    fi
    exit 0
}
trap cleanup INT TERM

wait $NGROK_PID
