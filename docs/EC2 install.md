# Instalación en AWS EC2

Stack: **Laravel Octane + FrankenPHP (Caddy) + Valkey + PostgreSQL 18**
Sistema operativo: **Ubuntu 24.04 LTS**

## Arquitectura

```
Internet (80/443)
      │
      ▼
 FrankenPHP
 ┌─────────────────────────────────────────┐
 │  Caddy (HTTP server)                    │
 │  - SSL/TLS automático (Let's Encrypt)   │
 │  - HTTP → HTTPS redirect                │
 │  - Archivos estáticos                   │
 │  - Compresión zstd / brotli / gzip      │
 │                                         │
 │  PHP Workers persistentes (Octane)      │
 │  - Sin bootstrap por request            │
 │  - Conexiones DB reutilizadas           │
 └─────────────────────────────────────────┘
      │
      ▼
 Valkey (127.0.0.1:6379)
 - Cache, sesiones, queues
      │
      ▼
 PostgreSQL (127.0.0.1:5432)
```

> FrankenPHP es Caddy con PHP embebido. Reemplaza a Nginx/Apache + PHP-FPM + Certbot en un solo proceso. No hay proxy adicional.

---

## Prerequisitos

- Instancia EC2 con Ubuntu 24.04 LTS (mínimo `t3.small`, recomendado `t3.medium`)
- Los registros DNS tipo A de `telochile.cl` y `www.telochile.cl` apuntando a la IP pública del servidor **antes de iniciar la instalación** (necesario para que Caddy obtenga el certificado SSL)
- Puerto 22 (SSH), 80 (HTTP) y 443 (HTTPS) abiertos en el Security Group

Verificar IP pública del servidor:
```bash
curl ifconfig.me
```

---

## Instalación

> El script completo está en [`EC2 install.sh`](./EC2%20install.sh). Los pasos a continuación lo explican sección por sección.

### 1. Preparación del sistema

```bash
sudo apt update && sudo apt upgrade -y

# Swap de 2GB — evita que bun run build congele el servidor
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile && sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

### 2. Repositorios y paquetes

Se agregan los repos de PostgreSQL, PHP (ondrej) y Valkey con el método moderno (`gpg --dearmor` + `signed-by`), sin el deprecado `apt-key add`.

Paquetes instalados:
| Paquete | Propósito |
|---------|-----------|
| `php8.5`, `php8.5-cli` | PHP + CLI |
| `php8.5-pgsql`, `php8.5-redis`, etc. | Extensiones PHP |
| `postgresql-18` | Base de datos |
| `valkey` | Cache / sesiones / colas (fork Redis) |
| `composer` | Gestor de dependencias PHP |

> No se instala nginx, certbot, php-fpm, Node.js ni Bun. FrankenPHP reemplaza el servidor web. Los assets JS se compilan en CI.

### 3. Valkey

```bash
# Configurar para escuchar solo en localhost
sudo sed -i 's/^bind .*/bind 127.0.0.1 -::1/' /etc/valkey/valkey.conf
sudo sed -i 's/^# maxmemory .*/maxmemory 256mb/' /etc/valkey/valkey.conf
sudo sed -i 's/^# maxmemory-policy .*/maxmemory-policy allkeys-lru/' /etc/valkey/valkey.conf
```

Variables en `.env`:
```dotenv
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

> Valkey es binariamente compatible con Redis. La extensión `php8.5-redis` funciona sin cambios.

### 4. Aplicación Laravel

```bash
git clone https://github.com/alejmendez/telo-web-laravel.git teloweb
cd teloweb
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate

# Instalar el binario de FrankenPHP (descarga desde GitHub releases)
php artisan octane:install --server=frankenphp --no-interaction
```

> Los assets de `public/build/` **no se generan aquí**. El pipeline de GitHub Actions los compila con Bun y los sube al servidor via SCP antes de ejecutar el SSH deploy.

### 5. Caddyfile

FrankenPHP usa un `Caddyfile` en la raíz del proyecto para configurar el servidor. Se apunta con la variable `LARAVEL_OCTANE_CADDYFILE` en `.env`.

```caddyfile
{
    email alejmendez.87@gmail.com   # Para Let's Encrypt

    frankenphp {
        num_threads auto
    }
}

telochile.cl, www.telochile.cl {
    root * /home/ubuntu/teloweb/public

    # Compresión (zstd > brotli > gzip)
    encode zstd br gzip

    # Archivos estáticos — caché de 1 año (Vite usa hashes por build)
    @static path_regexp \.(ico|css|js|gif|jpg|jpeg|png|svg|webp|woff|woff2|ttf|eot)$
    handle @static {
        header Cache-Control "public, max-age=31536000, immutable"
        file_server
    }

    request_body {
        max_size 20MB
    }

    # Workers persistentes de Octane
    php_server {
        worker {
            file /home/ubuntu/teloweb/public/frankenphp-worker.php
            num auto
        }
    }
}
```

Ventajas sobre Nginx:
- Caddy obtiene y renueva certificados SSL automáticamente (ACME)
- HTTP/3 (QUIC) habilitado por defecto
- Compresión brotli y zstd (no disponibles en Nginx sin módulo adicional)
- Los archivos estáticos los sirve Caddy directamente, sin tocar los workers PHP

### 6. Servicio systemd — Octane

```ini
[Unit]
Description=Laravel Octane (FrankenPHP)
After=network.target valkey.service postgresql.service

[Service]
User=ubuntu
Group=ubuntu
WorkingDirectory=/home/ubuntu/teloweb
ExecStart=/usr/bin/php artisan octane:start --server=frankenphp
Restart=always
RestartSec=5

# Permite enlazarse a puertos 80 y 443 sin ejecutar como root
AmbientCapabilities=CAP_NET_BIND_SERVICE
CapabilityBoundingSet=CAP_NET_BIND_SERVICE

# Caddy necesita estas variables para guardar certificados en el home del usuario
Environment="HOME=/home/ubuntu"
Environment="XDG_DATA_HOME=/home/ubuntu/.local/share"
Environment="XDG_CONFIG_HOME=/home/ubuntu/.config"
```

> `AmbientCapabilities` es más seguro que `setcap` sobre el binario: la capacidad es temporal y solo aplica a este servicio.

Los certificados se almacenan en `/home/ubuntu/.local/share/caddy/`.

### 7. Servicio systemd — Queue Worker

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target valkey.service

[Service]
User=ubuntu
WorkingDirectory=/home/ubuntu/teloweb
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5
```

---

## Gestión de servicios

```bash
# Ver estado
status_services

# Iniciar / detener
start_services
stop_services
restart_services

# Logs en tiempo real
logs_octane     # FrankenPHP + PHP errors
logs_queue      # Queue worker
logs_caddy      # Solo líneas de Caddy
```

---

## Actualizaciones

Los aliases son para uso manual de emergencia. El flujo normal de deploy es vía GitHub Actions (push a `main`).

| Alias | Cuándo usarlo |
|-------|--------------|
| `update_app` | Pull + composer + optimize (sin migraciones) |
| `update_app_with_migrations` | Pull + composer + migrate + optimize |

> Los assets JS **siempre** los sube GitHub Actions. Estos aliases solo actualizan PHP.

---

## Troubleshooting

### El certificado SSL no se obtiene
- Verificar que el DNS ya apunta al servidor: `dig telochile.cl +short`
- Ver logs de Caddy: `logs_caddy`
- Let's Encrypt tiene rate limits — en desarrollo/staging usar `acme_ca https://acme-staging-v02.api.letsencrypt.org/directory` en el bloque global del Caddyfile

### Error al enlazarse a puerto 80/443
- Verificar que `AmbientCapabilities` está en el servicio: `sudo systemctl cat octane`
- Reiniciar el daemon: `sudo systemctl daemon-reload && sudo systemctl restart octane`

### Workers PHP no responden
- Verificar que `public/frankenphp-worker.php` existe (generado por `octane:install`)
- Reinstalar: `php artisan octane:install --server=frankenphp --no-interaction`

### Valkey no conecta
```bash
sudo systemctl status valkey
valkey-cli ping   # Debe responder PONG
```

### Ver certificados almacenados
```bash
ls ~/.local/share/caddy/certificates/
```

---

## Servicios activos

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| FrankenPHP (octane) | 80, 443 | HTTP/HTTPS + PHP workers |
| Valkey | 127.0.0.1:6379 | Cache / sesiones / colas |
| PostgreSQL | 127.0.0.1:5432 | Base de datos |
| Queue Worker | — | Procesamiento de jobs en background |
