# Guía rápida para subir a producción (Railway)

## Qué vas a obtener

Un link público tipo:

- Trabajadores: `https://tu-app.up.railway.app/`
- Admin: `https://tu-app.up.railway.app/admin/login`

Gratis, suficiente para ~1 mes.

---

## Paso 1. Crear cuenta GitHub

1. Entra a https://github.com/signup
2. Crea tu cuenta

## Paso 2. Subir el proyecto a GitHub

Abre PowerShell en la carpeta del proyecto y ejecuta:

```powershell
cd c:\laragon\www\calendario
git init
git add .
git commit -m "Sistema de vacaciones institucional"
```

Luego en GitHub:

1. Click en **New repository**
2. Nombre: `calendario-vacaciones`
3. Déjalo **público**
4. **Create repository**

Después:

```powershell
git branch -M main
git remote add origin https://github.com/TU_USUARIO/calendario-vacaciones.git
git push -u origin main
```

(Cambia `TU_USUARIO` por tu usuario de GitHub)

## Paso 3. Desplegar en Railway

1. Entra a https://railway.app
2. Login con GitHub
3. **New Project** → **Deploy from GitHub repo**
4. Elige `calendario-vacaciones`
5. Espera a que cree el servicio

## Paso 4. Variables de entorno

En el servicio de Railway → **Variables** → agrega:

```
APP_NAME=Vacaciones RH
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=
DB_CONNECTION=sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
COMPOSER_ALLOW_SUPERUSER=1
```

### Generar APP_KEY

En tu PC:

```powershell
cd c:\laragon\www\calendario
php artisan key:generate --show
```

Copia el resultado completo (`base64:...`) y pégalo en `APP_KEY`.

## Paso 5. Dominio público

1. En Railway → tu servicio → **Settings** → **Networking**
2. Click **Generate Domain**
3. Copia la URL (ejemplo: `https://calendario-vacaciones-production.up.railway.app`)
4. Pégala en la variable `APP_URL`
5. Guarda / redeploy si hace falta

## Paso 6. Base de datos (recomendado)

Para que no se borren vacaciones al reiniciar:

1. En el proyecto Railway → **+ New** → **Database** → **Add PostgreSQL**
2. En Variables del servicio web cambia:

```
DB_CONNECTION=pgsql
```

Railway conecta solo con `DATABASE_URL`.

## Paso 7. Probar

- Trabajadores: `https://TU-URL/`
- Admin: `https://TU-URL/admin/login`
  - Email: `admin@calendario.test`
  - Contraseña: `password`

---

## Links para compartir

| Quién | Link |
|-------|------|
| Trabajadores | `https://TU-URL/` |
| Ingeniera | `https://TU-URL/admin/login` |

## Notas

- Cambia la contraseña del admin después del primer acceso (desde base de datos o te ayudo).
- Si Railway se duerme por inactividad en plan free, el primer click puede tardar unos segundos.
- Si quieres, en el siguiente mensaje te guío click por click con tu usuario de GitHub.
