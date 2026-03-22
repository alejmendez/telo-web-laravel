# Configuración AWS Console

Pasos manuales requeridos en la consola de AWS que no se pueden automatizar con scripts.

---

## 1. EC2 — Security Group

Abrir los puertos necesarios para la instancia EC2.

**Navegación:** EC2 → Instances → seleccionar instancia → Security → Security Groups → Edit inbound rules

| Tipo | Puerto | Origen | Descripción |
|------|--------|--------|-------------|
| SSH | 22 | Tu IP / 0.0.0.0/0 | Acceso SSH |
| HTTP | 80 | 0.0.0.0/0, ::/0 | Redirect a HTTPS (FrankenPHP) |
| HTTPS | 443 | 0.0.0.0/0, ::/0 | Tráfico web |

> Los puertos internos (5432 PostgreSQL, 6432 PgBouncer, 6379 Valkey) **no** deben abrirse. Solo son accesibles desde localhost.

---

## 2. IAM — Usuario para GitHub Actions

Crear un usuario IAM con permisos mínimos para el pipeline de CI/CD.

### 2.1 Crear usuario

**Navegación:** IAM → Users → Create user

- **User name:** `github-actions-telo`
- **Access type:** Programmatic access (no necesita acceso a consola)

### 2.2 Política de permisos

Crear una política personalizada con permisos mínimos:

**Navegación:** IAM → Policies → Create policy → JSON

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "CloudFrontInvalidation",
      "Effect": "Allow",
      "Action": "cloudfront:CreateInvalidation",
      "Resource": "arn:aws:cloudfront::ACCOUNT_ID:distribution/DISTRIBUTION_ID"
    }
  ]
}
```

Reemplazar `ACCOUNT_ID` y `DISTRIBUTION_ID` con los valores reales (se obtienen en el paso 3).

**Nombre de la política:** `github-actions-telo-policy`

### 2.3 Obtener credenciales

**Navegación:** IAM → Users → `github-actions-telo` → Security credentials → Create access key

- **Use case:** Application running outside AWS
- Guardar `Access key ID` y `Secret access key` — solo se muestran una vez

### 2.4 Agregar secrets en GitHub

**Navegación:** GitHub repo → Settings → Secrets and variables → Actions → New repository secret

| Secret | Valor |
|--------|-------|
| `EC2_HOST` | IP pública de la instancia EC2 |
| `EC2_USERNAME` | `ubuntu` |
| `EC2_SSH_KEY` | Contenido completo del archivo `.pem` (incluye `-----BEGIN...` y `-----END...`) |
| `AWS_ACCESS_KEY_ID` | Access key del usuario IAM |
| `AWS_SECRET_ACCESS_KEY` | Secret key del usuario IAM |
| `CLOUDFRONT_DISTRIBUTION_ID` | ID de la distribución (ej: `E1A2B3C4D5E6F7`) |

---

## 3. CloudFront — Distribución para assets estáticos

Sirve los archivos de `public/build/` (CSS, JS, fuentes, imágenes) desde edge locations globales sin pasar por el EC2.

### 3.1 Crear distribución

**Navegación:** CloudFront → Distributions → Create distribution

**Origin:**
| Campo | Valor |
|-------|-------|
| Origin domain | `telochile.cl` |
| Protocol | HTTPS only |
| HTTPS port | 443 |
| Name | `telochile-ec2` |

**Default cache behavior:**
| Campo | Valor |
|-------|-------|
| Viewer protocol policy | Redirect HTTP to HTTPS |
| Allowed HTTP methods | GET, HEAD |
| Cache policy | `CachingOptimized` |
| Origin request policy | `AllViewerExceptHostHeader` |

**Settings:**
| Campo | Valor |
|-------|-------|
| Price class | Use only North America, Europe, and South America |
| Default root object | *(dejar vacío)* |

> **No** agregar un CNAME alternativo — CloudFront debe usar su propio dominio (`xxxxxxxxxx.cloudfront.net`). La app referencia ese dominio via `ASSET_URL`.

### 3.2 Obtener el dominio de la distribución

Una vez creada (tarda ~5 min en desplegarse), copiar el dominio:

```
Distribution domain name: xxxxxxxxxx.cloudfront.net
Distribution ID:          E1A2B3C4D5E6F7
```

### 3.3 Configurar en el servidor

Editar el `.env` en EC2:

```bash
ssh ubuntu@EC2_IP
cd teloweb
nano .env
# Descomentar y completar:
# ASSET_URL=https://xxxxxxxxxx.cloudfront.net
```

Reiniciar Octane para que tome el cambio:

```bash
restart_services
```

### 3.4 Actualizar la política IAM

Volver a IAM y reemplazar `ACCOUNT_ID` y `DISTRIBUTION_ID` en la política creada en el paso 2.2 con los valores reales.

### 3.5 Verificar que funciona

Después del próximo deploy (push a `main`), verificar en el navegador que los assets cargan desde CloudFront:

```
DevTools → Network → click en un archivo .js → Headers → Response Headers
x-cache: Hit from cloudfront    ← cache hit ✓
x-cache: Miss from cloudfront   ← primera carga, se cachea
```

---

## 4. Route 53 (opcional) — DNS gestionado por AWS

Si el dominio `telochile.cl` está registrado en otro proveedor (NIC Chile, etc.), solo se necesita apuntar el DNS a la IP del EC2 desde el panel del proveedor actual. Route 53 es opcional.

Si se decide usar Route 53:

**Navegación:** Route 53 → Hosted zones → Create hosted zone

- **Domain name:** `telochile.cl`
- **Type:** Public hosted zone

Crear registros tipo A:

| Nombre | Tipo | Valor |
|--------|------|-------|
| `telochile.cl` | A | IP pública del EC2 |
| `www.telochile.cl` | A | IP pública del EC2 |

Copiar los 4 nameservers asignados y configurarlos en el registrador del dominio.

---

## Resumen de recursos AWS creados

| Recurso | Nombre/ID | Propósito |
|---------|-----------|-----------|
| EC2 Instance | `telo-production` | Servidor principal |
| Security Group | `telo-sg` | Firewall de la instancia |
| IAM User | `github-actions-telo` | CI/CD credentials |
| IAM Policy | `github-actions-telo-policy` | Permisos mínimos |
| CloudFront Distribution | `xxxxxxxxxx.cloudfront.net` | CDN para assets estáticos |
