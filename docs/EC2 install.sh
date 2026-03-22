#!/bin/bash
set -e  # Detener si cualquier comando falla

# ============================================================
# SCRIPT DE INSTALACIÓN - TELO WEB
# Stack: Laravel Octane + FrankenPHP (Caddy) + PgBouncer + Valkey + PostgreSQL
# Assets JS: compilados en CI (GitHub Actions) y subidos via SCP — no se usa Node/Bun en el servidor
# Ubuntu 24.04 LTS en AWS EC2
#
# IMPORTANTE: Verificar antes de ejecutar que los registros DNS
# de telochile.cl y www.telochile.cl apunten a la IP de este servidor.
#   curl ifconfig.me
# ============================================================

APP_DIR="/home/ubuntu/teloweb"
APP_USER="ubuntu"
APP_DOMAIN="telochile.cl"
ACME_EMAIL="alejmendez.87@gmail.com"
DB_NAME="teloweb"
DB_USER="postgres"
DB_PASSWORD="postgres"       # CAMBIAR EN PRODUCCIÓN
PGBOUNCER_PORT="6432"        # Puerto que expone PgBouncer a la app
POSTGRES_PORT="5432"         # Puerto interno de PostgreSQL (no expuesto a la app)


# ============================================================
# SISTEMA BASE
# ============================================================

sudo apt update
sudo apt upgrade -y

# Swap — solo necesario en t3.micro (1GB RAM). En t3.small o superior, omitir.
# sudo fallocate -l 1G /swapfile
# sudo chmod 600 /swapfile
# sudo mkswap /swapfile && sudo swapon /swapfile
# echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab


# ============================================================
# REPOSITORIOS
# ============================================================

sudo install -d /usr/share/keyrings

# PostgreSQL
curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    | sudo gpg --dearmor -o /usr/share/keyrings/postgresql.gpg
echo "deb [signed-by=/usr/share/keyrings/postgresql.gpg] https://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" \
    | sudo tee /etc/apt/sources.list.d/pgdg.list

# PHP (ondrej/php)
sudo add-apt-repository ppa:ondrej/php -y

# Valkey (Redis-compatible, open-source fork)
curl -fsSL https://packages.valkey.io/valkey-ubuntu/valkey.gpg \
    | sudo gpg --dearmor -o /usr/share/keyrings/valkey.gpg
echo "deb [signed-by=/usr/share/keyrings/valkey.gpg] https://packages.valkey.io/valkey-ubuntu $(lsb_release -cs) main" \
    | sudo tee /etc/apt/sources.list.d/valkey.list

sudo apt update


# ============================================================
# INSTALACIÓN DE PAQUETES
# FrankenPHP incluye su propio servidor — no se necesita nginx ni certbot
# ============================================================

sudo apt install -y \
    php8.5 php8.5-cli \
    php8.5-pgsql php8.5-gd php8.5-curl \
    php8.5-mbstring php8.5-xml php8.5-zip \
    php8.5-bcmath php8.5-intl php8.5-redis \
    postgresql-18 \
    pgbouncer \
    valkey \
    unzip curl git

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer


# ============================================================
# OPCACHE + JIT (PHP 8.x)
# Octane corre como proceso CLI — se configura en php8.5/cli/
# validate_timestamps=0 es seguro porque Octane reinicia los workers en deploy
# ============================================================

sudo tee /etc/php/8.5/cli/conf.d/99-performance.ini > /dev/null <<'OPCACHE_CONF'
; OPcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1

; JIT tracing — beneficia lógica de negocio y transformaciones de datos
; En workloads I/O-bound (queries DB) el impacto es menor, pero nunca negativo
opcache.jit=tracing
opcache.jit_buffer_size=64M
OPCACHE_CONF


# ============================================================
# CONFIGURACIÓN DE VALKEY
# ============================================================

sudo sed -i 's/^bind .*/bind 127.0.0.1 -::1/' /etc/valkey/valkey.conf
sudo sed -i 's/^# maxmemory .*/maxmemory 256mb/' /etc/valkey/valkey.conf
sudo sed -i 's/^# maxmemory-policy .*/maxmemory-policy allkeys-lru/' /etc/valkey/valkey.conf

sudo systemctl enable valkey
sudo systemctl start valkey


# ============================================================
# CLONAR Y CONFIGURAR LA APLICACIÓN
# ============================================================

git clone https://github.com/alejmendez/telo-web-laravel.git teloweb
cd teloweb

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate

# Los assets JS (public/build) son subidos por el pipeline de GitHub Actions via SCP.
# No se requiere Node.js ni Bun en el servidor.

# Configurar .env — Valkey (protocolo Redis)
sed -i 's|^APP_URL=.*|APP_URL=https://'"${APP_DOMAIN}"'|' .env
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=redis/' .env
sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env
sed -i 's/^REDIS_HOST=.*/REDIS_HOST=127.0.0.1/' .env
sed -i 's/^REDIS_PORT=.*/REDIS_PORT=6379/' .env
sed -i 's/^REDIS_PASSWORD=.*/REDIS_PASSWORD=null/' .env

# FrankenPHP/Octane
sed -i 's/^OCTANE_SERVER=.*/OCTANE_SERVER=frankenphp/' .env
sed -i 's/^OCTANE_WORKERS=.*/OCTANE_WORKERS=auto/' .env
sed -i 's/^OCTANE_MAX_REQUESTS=.*/OCTANE_MAX_REQUESTS=500/' .env
sed -i 's/^OCTANE_TASK_WORKERS=.*/OCTANE_TASK_WORKERS=6/' .env
echo "LARAVEL_OCTANE_CADDYFILE=${APP_DIR}/Caddyfile" >> .env

php artisan optimize
php artisan storage:link


# ============================================================
# BASE DE DATOS POSTGRESQL
# ============================================================

sudo -u postgres psql -c "ALTER USER ${DB_USER} WITH PASSWORD '${DB_PASSWORD}';"
sudo -u postgres psql -c "CREATE DATABASE ${DB_NAME};"

# La app se conecta a PgBouncer (PGBOUNCER_PORT), no a PostgreSQL directamente
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i "s/^DB_PORT=.*/DB_PORT=${PGBOUNCER_PORT}/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

composer install --optimize-autoloader --no-dev
composer dump-autoload
php artisan optimize
php artisan migrate --force
php artisan app:sync-permissions


# ============================================================
# POSTGRESQL — TUNING DE PERFORMANCE
#
# La configuración por defecto de PostgreSQL es extremadamente conservadora.
# Estos valores están calibrados para t3.small (2GB RAM).
# Para t3.medium (4GB): shared_buffers=1GB, effective_cache_size=3GB, work_mem=32MB
# Para t3.large  (8GB): shared_buffers=2GB, effective_cache_size=6GB, work_mem=64MB
# ============================================================

sudo tee /etc/postgresql/18/main/conf.d/performance.conf > /dev/null <<'PG_PERF'
# Memoria
shared_buffers = 512MB                # 25% de RAM — cache de páginas en memoria
effective_cache_size = 1536MB         # 75% de RAM — estimación para el query planner
work_mem = 16MB                       # por operación sort/hash (puede multiplicarse por conexiones)
maintenance_work_mem = 128MB          # para VACUUM, CREATE INDEX, ALTER TABLE

# Write-Ahead Log
wal_buffers = 16MB
checkpoint_completion_target = 0.9
max_wal_size = 1GB

# Conexiones — PgBouncer gestiona el pooling, PostgreSQL ve pocas conexiones reales
max_connections = 50

# Planner — SSDs tienen I/O aleatorio casi tan rápido como secuencial
random_page_cost = 1.1
effective_io_concurrency = 200

# Logging de queries lentas (identificar cuellos de botella)
log_min_duration_statement = 200      # loggear queries > 200ms
PG_PERF

sudo systemctl reload postgresql


# ============================================================
# PGBOUNCER — CONNECTION POOLER PARA POSTGRESQL
#
# Problema que resuelve: Octane tiene N workers persistentes, cada uno
# con una conexión DB abierta. PostgreSQL es costoso por conexión (~5MB RAM).
# PgBouncer agrupa todas esas conexiones en un pool pequeño y eficiente.
#
# pool_mode=transaction: la conexión se devuelve al pool al terminar cada
# transacción. Es el modo más eficiente y compatible con Octane.
#
# Flujo: App → PgBouncer (:6432) → PostgreSQL (:5432)
# ============================================================

sudo tee /etc/pgbouncer/pgbouncer.ini > /dev/null <<PGBOUNCER_CONF
[databases]
${DB_NAME} = host=127.0.0.1 port=${POSTGRES_PORT} dbname=${DB_NAME}

[pgbouncer]
listen_addr = 127.0.0.1
listen_port = ${PGBOUNCER_PORT}

; scram-sha-256: compatibilidad con PostgreSQL 14+ (usa SCRAM por defecto)
auth_type = scram-sha-256
auth_file = /etc/pgbouncer/userlist.txt

; transaction mode — más eficiente, compatible con Octane workers
pool_mode = transaction
server_reset_query = DISCARD ALL

max_client_conn = 1000
default_pool_size = 20
min_pool_size = 5
reserve_pool_size = 5
reserve_pool_timeout = 3

; Reducir logs en producción
log_connections = 0
log_disconnections = 0
PGBOUNCER_CONF

# userlist.txt — contraseña en texto plano (PgBouncer 1.18+ genera SCRAM internamente)
# El archivo debe ser legible solo por root/postgres
echo "\"${DB_USER}\" \"${DB_PASSWORD}\"" | sudo tee /etc/pgbouncer/userlist.txt > /dev/null
sudo chmod 640 /etc/pgbouncer/userlist.txt
sudo chown postgres:postgres /etc/pgbouncer/userlist.txt

sudo systemctl enable pgbouncer
sudo systemctl start pgbouncer


# ============================================================
# FRANKENPHP — INSTALAR BINARIO Y CONFIGURAR
# ============================================================

# Descarga el binario de FrankenPHP vía Octane
php artisan octane:install --server=frankenphp --no-interaction

# Crear directorio de datos de Caddy (certificados Let's Encrypt)
mkdir -p /home/ubuntu/.local/share/caddy
mkdir -p /home/ubuntu/.config/caddy

# Caddyfile — FrankenPHP lo usa como servidor web completo:
# - SSL automático via Let's Encrypt (no necesita certbot)
# - HTTP → HTTPS redirect automático
# - Archivos estáticos servidos directamente (sin pasar por PHP)
# - Compresión zstd/brotli/gzip
cat > "${APP_DIR}/Caddyfile" <<CADDYFILE
{
    email ${ACME_EMAIL}

    frankenphp {
        num_threads auto
    }
}

${APP_DOMAIN}, www.${APP_DOMAIN} {
    root * ${APP_DIR}/public

    # Compresión — prioridad: zstd > brotli > gzip
    encode zstd br gzip

    # Archivos estáticos con caché máxima
    # Vite genera nombres con hash único por build, sin riesgo de cache stale
    @static path_regexp static \.(ico|css|js|gif|jpg|jpeg|png|svg|webp|woff|woff2|ttf|eot)$
    handle @static {
        header Cache-Control "public, max-age=31536000, immutable"
        file_server
    }

    # Tamaño máximo de carga de archivos
    request_body {
        max_size 20MB
    }

    # PHP via workers persistentes de FrankenPHP/Octane
    php_server {
        worker {
            file ${APP_DIR}/public/frankenphp-worker.php
            num auto
        }
    }
}
CADDYFILE


# ============================================================
# SERVICIO SYSTEMD: LARAVEL OCTANE (FrankenPHP)
#
# AmbientCapabilities permite que el proceso del usuario ubuntu
# pueda enlazarse a los puertos 80 y 443 sin ejecutar como root.
# Esto es más seguro que usar setcap sobre el binario.
# ============================================================

sudo tee /etc/systemd/system/octane.service > /dev/null <<OCTANE_SERVICE
[Unit]
Description=Laravel Octane (FrankenPHP)
After=network.target valkey.service pgbouncer.service

[Service]
User=${APP_USER}
Group=${APP_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php artisan octane:start --server=frankenphp
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

# Permite enlazarse a puertos < 1024 (80, 443) sin root
AmbientCapabilities=CAP_NET_BIND_SERVICE
CapabilityBoundingSet=CAP_NET_BIND_SERVICE

# Variables de entorno para Caddy
Environment="HOME=/home/${APP_USER}"
Environment="XDG_DATA_HOME=/home/${APP_USER}/.local/share"
Environment="XDG_CONFIG_HOME=/home/${APP_USER}/.config"

[Install]
WantedBy=multi-user.target
OCTANE_SERVICE

sudo systemctl daemon-reload
sudo systemctl enable octane
sudo systemctl start octane


# ============================================================
# SERVICIO SYSTEMD: LARAVEL QUEUE WORKER
# ============================================================

sudo tee /etc/systemd/system/queue-worker.service > /dev/null <<QUEUE_SERVICE
[Unit]
Description=Laravel Queue Worker
After=network.target valkey.service pgbouncer.service

[Service]
User=${APP_USER}
Group=${APP_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
QUEUE_SERVICE

sudo systemctl daemon-reload
sudo systemctl enable queue-worker
sudo systemctl start queue-worker


# ============================================================
# ALIASES DE CONVENIENCIA
# ============================================================

echo -e '\nif [ -f ~/.bash_aliases ]; then\n    . ~/.bash_aliases\nfi\n' >> ~/.bashrc
touch ~/.bash_aliases

cat >> ~/.bash_aliases <<'ALIASES'
# Gestión de servicios (sin nginx — FrankenPHP maneja todo)
alias stop_services="sudo systemctl stop octane queue-worker"
alias start_services="sudo systemctl start valkey pgbouncer octane queue-worker"
alias restart_services="stop_services && start_services"
alias status_services="sudo systemctl status octane queue-worker pgbouncer valkey"

# Actualizaciones — los assets JS los sube GitHub Actions, no se compilan aquí
alias update_app="stop_services && git pull && composer install --optimize-autoloader --no-dev && composer dump-autoload && php artisan optimize && start_services"
alias update_app_with_migrations="stop_services && git pull && composer install --optimize-autoloader --no-dev && php artisan migrate --force && php artisan optimize && start_services"

# Logs
alias logs_octane="sudo journalctl -u octane -f"
alias logs_queue="sudo journalctl -u queue-worker -f"
alias logs_caddy="sudo journalctl -u octane -f --grep='caddy'"
alias logs_pgbouncer="sudo journalctl -u pgbouncer -f"

# PgBouncer — estadísticas en tiempo real
alias pgb_stats="sudo -u postgres psql -h 127.0.0.1 -p 6432 -d pgbouncer -c 'SHOW POOLS;'"
alias pgb_clients="sudo -u postgres psql -h 127.0.0.1 -p 6432 -d pgbouncer -c 'SHOW CLIENTS;'"
ALIASES

source ~/.bashrc

echo ""
echo "============================================================"
echo "  Instalación completada."
echo "  Stack: FrankenPHP + PgBouncer + Valkey + PostgreSQL"
echo "  Servicios: octane, queue-worker, pgbouncer, valkey"
echo "  Assets JS: compilados en CI y desplegados via GitHub Actions + SCP"
echo "  SSL: gestionado automáticamente por FrankenPHP/Caddy"
echo "  Certificados: ~/.local/share/caddy/"
echo "  DB: App → PgBouncer (:6432) → PostgreSQL (:5432)"
echo "============================================================"
