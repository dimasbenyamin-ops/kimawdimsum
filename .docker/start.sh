#!/bin/sh

# Railway assigns a PORT environment variable. Nginx will listen on this port.
PORT=${PORT:-8080}
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/http.d/default.conf

# Cache configuration for production
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Run database migrations if needed
# php artisan migrate --force

# Start Supervisor (which starts Nginx and PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
