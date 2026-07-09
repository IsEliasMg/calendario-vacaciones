# Calendario de Vacaciones

Sistema web para que el personal registre vacaciones y la ingeniera las vea en un calendario general.

- **Sin MySQL**: usa SQLite (un archivo).
- **Empleados**: entran por un link, escriben su nombre y eligen días (sin login).
- **Admin**: panel con usuario/contraseña, calendario, empleados y configuración.

## Arranque local (Laragon / Windows)

```bash
cd c:\laragon\www\calendario
composer install
copy .env.example .env
php artisan key:generate
# Si no existe el archivo:
# type nul > database\database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Abre: http://127.0.0.1:8000

## Credenciales admin

| Campo | Valor |
|-------|-------|
| URL | `/admin/login` |
| Email | `admin@calendario.test` |
| Contraseña | `password` |

## Compartir link público (~1 mes)

Sigue la guía: [DEPLOY.md](DEPLOY.md)

Resumen:

1. Sube a GitHub.
2. Despliega en [Railway](https://railway.app) (gratis).
3. Comparte el link con los trabajadores.
4. La ingeniera entra a `/admin/login`.

## Flujo

1. Admin crea empleados en **Empleados**.
2. Comparte el link público.
3. Cada trabajador escribe su nombre y selecciona días.
4. Admin ve todo en **Calendario**.
