# Sistema de Matriculados

Sistema web para administrar el padrón de un colegio profesional: matriculados, usuarios de acceso, estados de cuenta, ubicaciones y reportes. Proyecto de portfolio — todos los datos de la demo son ficticios.

**Stack:** Laravel 12 · Inertia.js · Vue 3 + TypeScript · Tailwind CSS 4 · Fortify (auth + 2FA) · Pest · SQLite (o MySQL)

## Qué incluye

| Módulo | Admin | Staff | Matriculado |
|---|:-:|:-:|:-:|
| Panel con estadísticas reales (total, morosos, usuarios activos) | ✅ | ✅ | — |
| **Matriculados**: alta, edición, baja, búsqueda por matrícula/nombre/DNI/email, foto | ✅ (con baja) | ✅ (sin baja) | — |
| **Reportes**: por matriculados, ubicación, estados y personalizado, con exportación CSV / Excel | ✅ | ✅ | — |
| **Usuarios** del sistema: alta, edición, activar/desactivar, baja | ✅ | — | — |
| **Provincias**, **Ciudades** y **Estados de cuenta**: ABM completo | ✅ | — | — |
| Credencial digital descargable en PDF | — | — | ✅ |

Reglas de negocio destacadas:

- Al crear un matriculado se crea su usuario de acceso en una transacción (contraseña inicial = DNI).
- Selects dependientes provincia → ciudad, y el servidor valida que la ciudad pertenezca a la provincia.
- No se puede eliminar una provincia, ciudad o estado que tenga matriculados asociados.
- Los usuarios desactivados no pueden iniciar sesión; un admin no puede desactivarse, eliminarse ni quitarse el rol a sí mismo.
- Registro público deshabilitado: las cuentas las crea un Admin o Staff.

## Cuentas de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | `admin@sistema.com` | `admin123` |
| Personal (Staff) | `staff@sistema.com` | `staff123` |
| Matriculado | `juan@matriculado.com` | `juan123` |

La base se siembra con 36 matriculados repartidos en varias provincias y estados (los datos son siempre los mismos, sin aleatoriedad).

## Instalación

Requisitos: PHP 8.2+, Composer, Node 20+.

```bash
git clone https://github.com/YamilCr/sistema-matriculados.git
cd sistema-matriculados

composer install
npm install

cp .env.example .env
php artisan key:generate

# Base de datos (SQLite, sin configurar nada más)
touch database/database.sqlite
# En .env: DB_CONNECTION=sqlite

php artisan migrate --seed
php artisan storage:link        # para las fotos de perfil

npm run build                   # o `npm run dev` para desarrollo
php artisan serve
```

Abrí http://127.0.0.1:8000 e ingresá con alguna de las cuentas de prueba.

Para resetear la demo a su estado inicial: `php artisan migrate:fresh --seed`.

### Usar MySQL en lugar de SQLite

Creá la base y completá `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`.

## Despliegue en Render (demo online)

El repositorio incluye `Dockerfile`, `docker/start.sh` y `render.yaml`, así que se publica sin configurar nada a mano:

1. Subí el código a GitHub (rama `main` o la que quieras publicar).
2. En [render.com](https://render.com) → **New +** → **Blueprint** → elegí este repositorio.
3. Render lee `render.yaml`, construye la imagen (tarda unos minutos) y publica el servicio gratuito.
4. Abrí la URL `https://sistema-matriculados-xxxx.onrender.com` y usá los botones de acceso rápido del login.

Cómo funciona:

- Usa **SQLite**: en cada inicio del servicio se recrea la base y se cargan los datos de ejemplo (`migrate:fresh --seed`), por lo que la demo siempre arranca limpia.
- El plan gratuito **se duerme tras 15 minutos sin visitas** y la primera visita posterior tarda unos 30-50 segundos en despertarlo (y reinicia los datos de demo).
- `APP_DEMO=true` activa los botones de acceso rápido. `APP_KEY` se genera sola al iniciar; si querés una fija, definila como variable de entorno en Render.

## Tests

```bash
npm run build        # los tests de páginas necesitan el manifest de Vite
php artisan test
```

Cubren autenticación, permisos por rol, el ABM de tablas maestras, matriculados y usuarios.

## Estructura útil

- `routes/web.php` — rutas agrupadas por rol (`auth`, `staff`, `admin`).
- `app/Http/Controllers/` — un controlador por módulo.
- `resources/js/pages/` — páginas Inertia; `components/master/MasterCrud.vue` es el ABM reutilizable de las tablas maestras.
- `resources/js/lib/exportData.ts` — exportación a CSV / Excel.
- `database/seeders/` — datos de la demo.
