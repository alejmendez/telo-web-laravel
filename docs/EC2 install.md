# Instalación en AWS EC2

Stack: **Laravel Octane + FrankenPHP (Caddy) + PgBouncer + Valkey + PostgreSQL 18**
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
 │  - Archivos estáticos (sin tocar PHP)   │
 │  - Compresión zstd / brotli / gzip      │
 │  - HTTP/3 (QUIC)                        │
 │                                         │
 │  PHP Workers persistentes (Octane)      │
 │  - Sin bootstrap por request            │
 │  - OPcache + JIT activado              │
 └─────────────────────────────────────────┘
      │
      ▼
 Valkey (127.0.0.1:6379)
 - Cache, sesiones, queues
      │
      ▼
 PgBouncer (127.0.0.1:6432)        ← la app apunta aquí
 - Connection pooling (pool_mode=transaction)
 - Agrupa N workers en ~20 conexiones reales
      │
      ▼
 PostgreSQL (127.0.0.1:5432)
```

> FrankenPHP es Caddy con PHP embebido. Reemplaza Nginx + PHP-FPM + Certbot en un solo proceso. PgBouncer evita que los workers de Octane saturen las conexiones de PostgreSQL.

---

## Prerequisitos

- Instancia EC2 con Ubuntu 24.04 LTS (mínimo `t3.small`, recomendado `t3.medium`)
- Los registros DNS tipo A de `telochile.cl` y `www.telochile.cl` apuntando a la IP pública del servidor **antes de iniciar la instalación** (Caddy los necesita para obtener el certificado SSL automáticamente)
- Puertos 22 (SSH), 80 (HTTP) y 443 (HTTPS) abiertos en el Security Group

Verificar IP pública:
```bash
curl ifconfig.me
```

---

## Instalación

> El script completo está en [`EC2 install.sh`](./EC2%20install.sh). Los pasos a continuación lo explican sección por sección.

### 1. Preparación del sistema

```bash
sudo apt update && sudo apt upgrade -y

# Swap — solo necesario en t3.micro (1GB RAM)
# sudo fallocate -l 1G /swapfile && sudo mkswap /swapfile && sudo swapon /swapfile
```

### 2. Repositorios y paquetes

Se agregan los repos de PostgreSQL, PHP (ondrej) y Valkey con `gpg --dearmor` + `signed-by` (sin el deprecado `apt-key add`).

| Paquete | Propósito |
|---------|-----------|
| `php8.5`, `php8.5-cli` | PHP + CLI |
| `php8.5-pgsql`, `php8.5-redis`, etc. | Extensiones PHP |
| `postgresql-18` | Base de datos |
| `pgbouncer` | Connection pooler para PostgreSQL |
| `valkey` | Cache / sesiones / colas (fork Redis) |
| `composer` | Gestor de dependencias PHP |

> No se instala nginx, certbot, php-fpm, Node.js ni Bun. FrankenPHP reemplaza el servidor web. Los assets JS se compilan en CI.

### 3. OPcache + JIT

Configurado en `/etc/php/8.5/cli/conf.d/99-performance.ini` (Octane corre como proceso CLI):

```ini
; OPcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0      ; seguro porque Octane reinicia workers en deploy

; JIT tracing — beneficia lógica de negocio y transformaciones de datos
opcache.jit=tracing
opcache.jit_buffer_size=64M
```

> `validate_timestamps=0` es crítico en producción: evita que PHP revise el filesystem en cada request. Los workers de Octane se reinician en cada deploy, por lo que el bytecode se recompila automáticamente.

### 4. Valkey

```bash
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

### 5. Aplicación Laravel

```bash
git clone https://github.com/alejmendez/telo-web-laravel.git teloweb
cd teloweb
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate

# Instalar el binario de FrankenPHP (descarga desde GitHub releases)
php artisan octane:install --server=frankenphp --no-interaction
```

> Los assets de `public/build/` **no se generan aquí**. El pipeline de GitHub Actions los compila con Bun y los sube al servidor via SCP antes del SSH deploy.

### 6. PostgreSQL — tuning de performance

La configuración por defecto de PostgreSQL es muy conservadora. Se crea `/etc/postgresql/18/main/conf.d/performance.conf`:

```ini
# Valores para t3.small (2GB RAM) — ajustar para instancias mayores
shared_buffers = 512MB          # 25% de RAM
effective_cache_size = 1536MB   # 75% de RAM (estimación para el planner)
work_mem = 16MB                 # por operación sort/hash
maintenance_work_mem = 128MB    # para VACUUM, CREATE INDEX

wal_buffers = 16MB
checkpoint_completion_target = 0.9
max_wal_size = 1GB

max_connections = 50            # PgBouncer limita las conexiones reales

random_page_cost = 1.1          # SSDs: I/O aleatorio ≈ I/O secuencial
effective_io_concurrency = 200

log_min_duration_statement = 200  # loggear queries > 200ms en /var/log/postgresql/
```

| Instancia | `shared_buffers` | `effective_cache_size` | `work_mem` |
|-----------|-----------------|----------------------|------------|
| t3.small (2GB) | 512MB | 1536MB | 16MB |
| t3.medium (4GB) | 1GB | 3GB | 32MB |
| t3.large (8GB) | 2GB | 6GB | 64MB |

### 7. PgBouncer

**El problema que resuelve:** con Octane corriendo N workers persistentes, cada worker mantiene una conexión abierta a PostgreSQL (~5MB RAM por conexión). PgBouncer actúa como proxy y agrupa todas esas conexiones en un pool pequeño.

```
Sin PgBouncer:  App (8 workers) → PostgreSQL (8 conexiones abiertas permanentemente)
Con PgBouncer:  App (8 workers) → PgBouncer → PostgreSQL (máx. 20 conexiones, compartidas)
```

Configuración (`/etc/pgbouncer/pgbouncer.ini`):

```ini
[databases]
teloweb = host=127.0.0.1 port=5432 dbname=teloweb

[pgbouncer]
listen_addr = 127.0.0.1
listen_port = 6432

auth_type = scram-sha-256
auth_file = /etc/pgbouncer/userlist.txt

; transaction mode: la conexión se devuelve al pool al terminar cada transacción
; Es el modo más eficiente y compatible con Octane workers persistentes
pool_mode = transaction
server_reset_query = DISCARD ALL

max_client_conn = 1000
default_pool_size = 20
min_pool_size = 5
```

La app apunta a PgBouncer, no a PostgreSQL directamente:
```dotenv
DB_PORT=6432   ; PgBouncer
; PostgreSQL sigue en :5432 pero solo PgBouncer lo usa
```

> **Nota:** `pool_mode = transaction` no soporta `SET` statements persistentes, advisory locks, ni `LISTEN/NOTIFY`. Si se necesitan, usar `pool_mode = session`.

### 8. Octane — workers y max requests

```dotenv
OCTANE_WORKERS=auto          # usa todos los CPUs disponibles
OCTANE_MAX_REQUESTS=500      # reinicia el worker cada 500 requests (previene memory leaks)
OCTANE_TASK_WORKERS=6        # workers para tareas concurrentes (Octane::concurrently)
```

`OCTANE_MAX_REQUESTS=500` es clave en producción: aunque Octane mantiene el estado entre requests, después de N requests el worker se reinicia limpiamente para evitar acumulación de memoria.

### 9. Caddyfile

FrankenPHP usa un `Caddyfile` en la raíz del proyecto. Se referencia con `LARAVEL_OCTANE_CADDYFILE` en `.env`.

```caddyfile
{
    email alejmendez.87@gmail.com

    frankenphp {
        num_threads auto
    }
}

telochile.cl, www.telochile.cl {
    root * /home/ubuntu/teloweb/public

    encode zstd br gzip

    @static path_regexp \.(ico|css|js|gif|jpg|jpeg|png|svg|webp|woff|woff2|ttf|eot)$
    handle @static {
        header Cache-Control "public, max-age=31536000, immutable"
        file_server
    }

    request_body {
        max_size 20MB
    }

    php_server {
        worker {
            file /home/ubuntu/teloweb/public/frankenphp-worker.php
            num auto
        }
    }
}
```

### 10. Servicios systemd

**Octane** — depende de `pgbouncer.service` (no de `postgresql.service` directamente):
```ini
After=network.target valkey.service pgbouncer.service
AmbientCapabilities=CAP_NET_BIND_SERVICE   ; permite bind a puertos 80/443 sin root
```

**Queue Worker** — también pasa por PgBouncer:
```ini
After=network.target valkey.service pgbouncer.service
```

---

## Gestión de servicios

```bash
status_services     # octane, queue-worker, pgbouncer, valkey
start_services
stop_services
restart_services

# Logs
logs_octane         # FrankenPHP + PHP errors
logs_queue          # Queue worker
logs_caddy          # Solo líneas de Caddy
logs_pgbouncer      # PgBouncer

# Estadísticas de PgBouncer en tiempo real
pgb_stats           # SHOW POOLS — ver pool de conexiones
pgb_clients         # SHOW CLIENTS — ver clientes conectados
```

---

## Actualizaciones

Los aliases son para uso manual de emergencia. El flujo normal es vía GitHub Actions (push a `main`).

| Alias | Cuándo usarlo |
|-------|--------------|
| `update_app` | Pull + composer + optimize (sin migraciones) |
| `update_app_with_migrations` | Pull + composer + migrate + optimize |

> Los assets JS **siempre** los sube GitHub Actions. Estos aliases solo actualizan PHP.

---

## Servicios activos

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| FrankenPHP (octane) | 80, 443 | HTTP/HTTPS + PHP workers + SSL automático |
| Valkey | 127.0.0.1:6379 | Cache / sesiones / colas |
| PgBouncer | 127.0.0.1:6432 | Connection pooler → PostgreSQL |
| PostgreSQL | 127.0.0.1:5432 | Base de datos (solo accesible via PgBouncer) |
| Queue Worker | — | Procesamiento de jobs en background |

---

## Optimizaciones pendientes (manuales)

Estas no se pueden automatizar en el script de instalación porque dependen de datos de runtime o de configuración externa.

### CloudFront CDN (recomendado)

Una vez creada la distribución CloudFront apuntando a `telochile.cl`:

```dotenv
# .env en producción
ASSET_URL=https://xxxxxxxxxx.cloudfront.net
```

Los assets de `public/build/` (CSS/JS con hash Vite) se servirán desde edge locations globales. Cache hit rate cercano al 100% por los headers `immutable`.

### Índices en PostgreSQL

Identificar queries lentas en `/var/log/postgresql/postgresql-18-main.log` (se loggan las de >200ms) y agregar índices en las migraciones:

```php
// Ejemplos comunes para el modelo CRM
$table->index(['status', 'created_at']);
$table->index('professional_id');
$table->index(['customer_id', 'status']);
```

### Tuning de PostgreSQL para instancias mayores

Si se escala a `t3.medium` (4GB) o superior, editar `/etc/postgresql/18/main/conf.d/performance.conf` con los valores de la tabla de la sección 6 y recargar:

```bash
sudo systemctl reload postgresql
```

---

## Troubleshooting

### El certificado SSL no se obtiene
- Verificar DNS: `dig telochile.cl +short`
- Ver logs de Caddy: `logs_caddy`
- Let's Encrypt tiene rate limits — en staging usar `acme_ca https://acme-staging-v02.api.letsencrypt.org/directory` en el bloque global del Caddyfile

### Error al enlazarse a puerto 80/443
```bash
sudo systemctl cat octane   # verificar AmbientCapabilities
sudo systemctl daemon-reload && sudo systemctl restart octane
```

### Workers PHP no responden
```bash
# Verificar que el worker file existe
ls public/frankenphp-worker.php

# Reinstalar si falta
php artisan octane:install --server=frankenphp --no-interaction
```

### PgBouncer no conecta
```bash
sudo systemctl status pgbouncer
# Verificar que el password en userlist.txt coincide con PostgreSQL
psql -h 127.0.0.1 -p 6432 -U postgres -d teloweb
```

### Valkey no conecta
```bash
sudo systemctl status valkey
valkey-cli ping   # debe responder PONG
```

### Ver certificados SSL
```bash
ls ~/.local/share/caddy/certificates/
```
