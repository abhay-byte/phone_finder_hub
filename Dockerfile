# Stage 1: Build Frontend Assets
FROM node:20 AS node_builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Build PHP Application
FROM php:8.4-apache

# Install system dependencies (PHP extensions + Python + native libs for rembg/onnxruntime)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libsqlite3-dev \
    libicu-dev \
    libpq-dev \
    # Python & pip
    python3 \
    python3-pip \
    python3-venv \
    # Native libs required by rembg / onnxruntime / Pillow
    libgl1 \
    libglib2.0-0 \
    libgomp1 \
    # Playwright system deps (Chromium)
    libnss3 \
    libxss1 \
    libatk-bridge2.0-0 \
    libdrm2 \
    libxkbcommon0 \
    libgbm1 \
    libasound2 \
    && docker-php-ext-install pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd intl \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Allow .htaccess to work
RUN echo '<Directory /var/www/html/public/>' > /etc/apache2/conf-available/override.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/override.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/override.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www/html

# Copy compiled frontend assets from node_builder
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN composer dump-autoload --optimize

# ── Python dependencies ────────────────────────────────────────────────────────
# Install into a venv so pip doesn't conflict with Debian system packages
RUN python3 -m venv /opt/phonefinder-venv \
    && /opt/phonefinder-venv/bin/pip install --upgrade pip \
    && /opt/phonefinder-venv/bin/pip install --no-cache-dir -r /var/www/html/python/requirements.txt

# Install Playwright Chromium browser (used by nanoreview & shopping link scrapers)
RUN /opt/phonefinder-venv/bin/playwright install chromium --with-deps 2>/dev/null || true

# Make the venv python available system-wide via a symlink
RUN ln -sf /opt/phonefinder-venv/bin/python3 /usr/local/bin/python3 \
    && ln -sf /opt/phonefinder-venv/bin/python3 /usr/local/bin/python

# Env var so AdminController always knows where the venv python lives
ENV PYTHON_BIN=/opt/phonefinder-venv/bin/python3
ENV PYTHON_SCRIPTS_PATH=/var/www/html/python
# ──────────────────────────────────────────────────────────────────────────────

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
