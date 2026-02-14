#!/bin/bash
set -e

echo "Starting Docker Entrypoint on Render..."

# Configure Apache to listen on $PORT (Render default: 10000)
PORT=${PORT:-80}
echo "Configuring Apache to listen on port $PORT..."
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Ensure persistent database directory exists
# Render Disk mount path (as defined in render.yaml)
DB_DIR="/var/lib/data"
DB_FILE="$DB_DIR/database.sqlite"

# Check if mounted volume is available
if [ ! -d "$DB_DIR" ]; then
    echo "Warning: Persistent volume not mounted at $DB_DIR. Falling back to local database (ephemeral)."
    DB_FILE="/var/www/html/database/database.sqlite"
    touch "$DB_FILE"
else
    echo "Using persistent volume at $DB_DIR"
    if [ ! -f "$DB_FILE" ]; then
        echo "Creating new database in persistent volume..."
        touch "$DB_FILE"
        chmod 666 "$DB_FILE"
        chown www-data:www-data "$DB_FILE"
        
        # Optionally copy existing seed DB
        if [ -f "/var/www/html/database/database.sqlite" ]; then
             echo "Copying initial database..."
             cp "/var/www/html/database/database.sqlite" "$DB_FILE"
        fi

        echo "Running initial migration..."
        php artisan migrate --force
    else
        echo "Existing database found."
        chmod 666 "$DB_FILE"
        chown www-data:www-data "$DB_FILE"
        
        echo "Running pending migrations..."
        php artisan migrate --force
    fi
fi

# Set Environment Variables for DB Connection
export DB_CONNECTION=sqlite
export DB_DATABASE="$DB_FILE"

# Cache configuration
echo "Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
