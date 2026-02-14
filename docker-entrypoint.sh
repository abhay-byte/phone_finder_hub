#!/bin/bash
set -e

echo "Starting Docker Entrypoint on Render (Ephemeral Mode)..."

# Configure Apache to listen on $PORT (Render default: 10000)
PORT=${PORT:-80}
echo "Configuring Apache to listen on port $PORT..."
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Database Configuration (Ephemeral)
DB_FILE="/var/www/html/database/database.sqlite"

echo "Using internal database at $DB_FILE"

if [ ! -f "$DB_FILE" ]; then
    echo "Creating new database..."
    touch "$DB_FILE"
else
    echo "Found existing database."
fi

# Ensure permissions
echo "Setting permissions for storage and database..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
if [ -f "$DB_FILE" ]; then
    chown www-data:www-data "$DB_FILE"
    chmod 666 "$DB_FILE"
fi
# Also ensure the directory is writable for SQLite WAL files
chown www-data:www-data /var/www/html/database
chmod 775 /var/www/html/database

# Run migrations (safe to run on every boot in ephemeral mode to ensure schema is up to date if we deployed new code)
echo "Running migrations..."
php artisan migrate --force

# Cache configuration
echo "Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
