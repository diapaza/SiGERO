# SiGERO

Sistema de Gestión de Equipos y Recursos Operativos con control de préstamos (salidas y retornos) para instituciones. Permite administrar inventario, registrar movimientos de préstamo, gestionar usuarios con roles y permisos, y mantener notificaciones automáticas de devoluciones vencidas.

## Stack

| Capa                     | Tecnología                                               |
| ------------------------ | -------------------------------------------------------- |
| Backend                  | Laravel 12 (PHP 8.2)                                     |
| Frontend                 | Vue 3 + TypeScript + Inertia.js 3                        |
| Estilos                  | Tailwind CSS 4                                           |
| Base de datos            | MySQL (SQLite en `.env.example` y tests)                 |
| Autenticación / permisos | Spatie Laravel Permission                                |
| Tablas                   | TanStack Vue Table 8                                     |
| Gráficos                 | ApexCharts                                               |
| Notificaciones           | Sistema de notificaciones de Laravel (driver `database`) |

## Requisitos

- PHP ≥ 8.2
- Composer
- Node.js ≥ 20 y npm
- MySQL (o SQLite para desarrollo rápido / pruebas)
- Extensiones PHP: `gd` (procesamiento de imágenes), `pdo_mysql`

## Instalación

**Atajo (todo en uno):**

```bash
composer setup
```

Ejecuta `composer install`, copia `.env.example` a `.env` (si no existe), genera `APP_KEY`, migra la base de datos, instala dependencias npm y compila el frontend.

**Manual paso a paso:**

```bash
# 1. Dependencias PHP
composer install

# 2. Entorno (ajusta el .env con tus credenciales)
cp .env.example .env

# 3. Clave de la aplicación
php artisan key:generate

# 4. Migraciones y seeders (roles, permisos y usuario admin)
php artisan migrate --seed

# 5. Enlace público de almacenamiento (fotos de objetos)
php artisan storage:link

# 6. Frontend
npm install
npm run build
```

## Variables de entorno

Todas las variables relevantes viven en `.env`. Las más importantes:

| Variable                      | Valor por defecto    | Descripción                                                                                             |
| ----------------------------- | -------------------- | ------------------------------------------------------------------------------------------------------- |
| `APP_NAME`                    | `SiGERO`             | Nombre de la aplicación (visible en la UI).                                                             |
| `APP_ENV`                     | `local`              | Entorno: `local`, `testing`, `production`.                                                              |
| `APP_DEBUG`                   | `true`               | En producción debe ser `false`.                                                                         |
| `APP_URL`                     | `http://localhost`   | URL raíz (en producción, la URL pública con HTTPS).                                                     |
| `APP_KEY`                     | —                    | Clave de cifrado (generada con `php artisan key:generate`).                                             |
| `APP_TIMEZONE`                | `UTC`                | Zona horaria institucional. SiGERO la fija en `America/Lima`; los movimientos se guardan en hora local. |
| `APP_LOCALE`                  | `en`                 | Locale de la aplicación (mensajes del framework).                                                       |
| `DB_CONNECTION`               | `sqlite`             | `mysql` en producción; `sqlite` en local/tests.                                                         |
| `DB_HOST` / `DB_PORT`         | `127.0.0.1` / `3306` | Conexión MySQL.                                                                                         |
| `DB_DATABASE`                 | —                    | Nombre de la base de datos (p. ej. `sigero`).                                                           |
| `DB_USERNAME` / `DB_PASSWORD` | `root` / —           | Credenciales MySQL.                                                                                     |
| `SESSION_DRIVER`              | `database`           | Sesiones en la tabla `sessions`.                                                                        |
| `QUEUE_CONNECTION`            | `database`           | Colas en la tabla `jobs` (notificaciones se envían en línea).                                           |
| `CACHE_STORE`                 | `database`           | Caché (incluye el caché de permisos de Spatie) en `cache` tables.                                       |
| `FILESYSTEM_DISK`             | `local`              | Disco de almacenamiento (`storage/app/public`).                                                         |
| `MAIL_MAILER`                 | `log`                | Correo a log por defecto.                                                                               |
| `BCRYPT_ROUNDS`               | `12`                 | Coste del hash de contraseñas.                                                                          |
| `VITE_APP_NAME`               | `${APP_NAME}`        | Expuesto al frontend por Vite.                                                                          |

> `.env` está en `.gitignore` y no debe subirse a git. `PROYECTO.md` y `AGENTS.md` también están ignorados (documentación local).

## Comandos

### Desarrollo

```bash
composer dev            # Laravel + colas + logs (pail) + Vite a la vez
# o en dos terminales:
php artisan serve
npm run dev
```

### Calidad

```bash
npm run typecheck       # vue-tsc --noEmit (tipos TS + Vue)
npm run lint            # ESLint sobre resources/js
npm run lint:fix        # ESLint con autocorrección
npm run format          # Prettier --write sobre resources/js
vendor/bin/pint         # PHP-CS-Fixer (Laravel preset)
php artisan test        # Suite PHPUnit (SQLite en memoria)
```

### Operación

```bash
php artisan app:notificar-vencidas   # Genera notificaciones de devoluciones vencidas (programado cada 6 h)
php artisan schedule:run             # Ejecuta el scheduler (requerido en producción vía cron)
php artisan storage:link             # Enlace público de fotos
```

## Credenciales iniciales

| Usuario | Contraseña  | Rol           |
| ------- | ----------- | ------------- |
| `admin` | `Admin123$` | Administrador |

> Cambia la contraseña del administrador tras el primer inicio de sesión.

## Estructura de carpetas

### Backend (`app/`)

```
app/
├── Console/Commands/        # Comandos Artisan propios (NotificarVencidas)
├── Enums/                   # TipoMovimientoEnum (salida | retorno)
├── Http/
│   ├── Controllers/         # BaseCrudController + controllers por entidad + ApiController
│   ├── Middleware/          # HandleInertiaRequests (props compartidas de Inertia)
│   └── Requests/            # FormRequests por entidad (validación de backend)
├── Models/                  # Eloquent: User, Role, Permission, Objeto, Movimiento, Categoria, Marca
├── Notifications/           # BaseNotification + 5 notificaciones
├── Rules/                   # UniqueIgnoringSoftDeletes
├── Services/                # Lógica de negocio (BaseCrudService + servicios de dominio)
└── Providers/               # AppServiceProvider (bindings de servicios, rate limiter login)
```

### Frontend (`resources/js/`)

```
resources/js/
├── app.ts                   # Bootstrap Inertia + Vue + Pinia + VueApexCharts + ZiggyVue
├── App.vue                  # Providers + toasts globales + diálogo global
├── components/
│   ├── base/                # UI reutilizable (BaseButton, BaseInput, BaseModal, BaseDataTable, ...)
│   ├── layout/              # AdminLayout, AppHeader, AppSidebar, AuthLayout, header/*, providers
│   ├── shared/              # ComponentCard, UserAvatar, GlobalDialog, TrashedEntities, ...
│   └── PermissionsModal.vue # Gestión de permisos directos por usuario
├── composables/             # useCrudIndex, useCrudColumns, useValidation, usePermissions, ...
├── constants/               # menú del sidebar (permissions)
├── icons/                   # Iconos SVG
├── stores/                  # Instancia de Pinia
├── types/                   # Interfaces TypeScript (models.ts) y declaraciones globales
├── utils/                   # Helpers (fechas)
└── views/                   # Páginas por módulo (se auto-resuelven con import.meta.glob)
```

## Flujo de datos: Laravel → Inertia → Vue

SiGERO es una SPA con **renderizado servidor** mediante Inertia.js: no hay API REST externa (salvo 2 endpoints internos de búsqueda). Cada página es un componente Vue que recibe sus datos como props del servidor.

```
 Navegador (SPA Vue 3)
      │  petición HTTP (Inertia: Link, router.visit, router.post...)
      ▼
 routes/web.php  ── middleware: web, auth, permission:..., throttle:login
      ▼
 HandleInertiaRequests  ── props compartidas (auth.user, notifications, flash, errors)
      ▼
 Controller  ── delega en un Service (validación vía FormRequest)
      ▼
 Service  ── lógica de negocio (invariantes, transacciones, notificaciones)
      ▼
 Model (Eloquent)  ── MySQL
      ▼
 Inertia::render('Vista', props)  ── JSON + nombre de la vista
      ▼
 app.ts  ── import.meta.glob('./views/**/*.vue') resuelve el componente
      ▼
 Componente Vue  ── recibe props, renderiza y emite acciones (router.*)
```

Detalles clave del ciclo:

1. **Navegación**: `<Link>` y `router.*` disparan peticiones XHR con la cabecera `X-Inertia`; el servidor responde JSON con `component` (nombre de la vista) y `props`.
2. **Props compartidas**: `app/Http/Middleware/HandleInertiaRequests.php` inyecta en cada respuesta `auth.user` (id, username, name, roles, permissions), `notifications` (últimas 10), `unreadNotifications`, `errors`, `flash` y `appName`.
3. **Resolución de vistas**: `resources/js/app.ts` carga todas las vistas con `import.meta.glob`; el nombre que devuelve el servidor (`Objetos/Index`, `Dashboard`, ...) se resuelve a `./views/{name}.vue`.
4. **Rutas en el frontend**: el helper global `route()` (Ziggy, via `@routes` en `resources/views/app.blade.php` + plugin `ZiggyVue`) genera URLs desde los nombres de ruta de Laravel.
5. **Validación**: `FormRequest` en backend + `useValidation` en frontend; los errores llegan como `errors` y se muestran en los campos (`form.errors.*`).
6. **Feedback**: `flash.success` / `flash.error` se comparten y `App.vue` los convierte en toasts (`vue-sonner`).
7. **Polling**: el Dashboard usa `usePoll(30000)` de Inertia para refrescar solo sus props.

Consulta `docs/rutas-inertia.md` para el detalle ruta → controlador → vista → props, y `docs/openapi.yaml` para los endpoints JSON internos.

## Decisiones técnicas clave

- **Controllers delgados**: la lógica de negocio vive en `app/Services`, cada uno registrado (bind) en `AppServiceProvider::register()`. Si creas un servicio nuevo, regístralo ahí.
- **CRUD base**: `BaseCrudController` / `BaseCrudService` para entidades simples (Categorías, Marcas); las entidades especiales (Objetos, Movimientos, Usuarios, Roles) sobrescriben solo lo necesario.
- **`disponible` derivado**: nunca se setea manualmente; `disponible = último movimiento != 'salida'`. `MovimientoService` lo recalcula al crear/actualizar/eliminar/restaurar movimientos.
- **Invariantes de préstamo**: no se permite una `salida` si el objeto ya está prestado ni un `retorno` si no lo está; la violación responde HTTP 422. El `objeto_id` de un movimiento es inmutable (evita disponibilidades inconsistentes).
- **Concurrencia**: la creación de movimientos y la generación de códigos de objeto se envuelven en transacciones con `lockForUpdate` (evita dobles salidas y códigos duplicados).
- **Soft deletes**: Marcas, Categorías, Objetos y Usuarios son soft-deleted con rutas `/trashed` y restauración. Los Roles no (Spatie).
- **Permisos**: Spatie Permission con middleware `permission:...` en las rutas y renderizado condicional en el frontend. Además de los permisos de rol, los usuarios pueden tener **permisos directos** (modal de permisos), que solo gestiona los directos (los del rol se muestran bloqueados).
- **Zona horaria**: `APP_TIMEZONE=America/Lima`; el frontend envía la hora local institucional (`utils/date.ts#toLocalDateTimeString`) y el backend la interpreta en la zona configurada.
- **Seguridad**: rate-limit en login (5/min por usuario+IP), manejadores de errores 419 (sesión expirada → signin) y 429; protección anti path-traversal en `ImageService`.
- **Imágenes**: `Intervention Image` (driver GD), redimensionadas a máx. 800 px, JPEG q80, renombradas a `{codigo}.jpg`; el directorio `storage/app/public/objetos` se crea automáticamente (`ensureDirectory`) y la foto previa se elimina al reemplazarla.
- **Tareas programadas**: `app:notificar-vencidas` cada 6 h (`routes/console.php`); en producción el cron debe llamar a `php artisan schedule:run` cada minuto.
- **Colas**: `QUEUE_CONNECTION=database`; las notificaciones se envían en línea (no requieren worker) pero la infraestructura está lista.

## Despliegue en producción

1. Configura `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, `APP_TIMEZONE=America/Lima`, `APP_KEY` y credenciales MySQL.
2. `composer install --no-dev --optimize-autoloader`
3. `php artisan migrate --force` (y `--seed` la primera vez).
4. `php artisan storage:link` (fotos de objetos).
5. `npm ci && npm run build`
6. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
7. Cron: `* * * * * php /ruta/a/artisan schedule:run >> /dev/null 2>&1`
8. Sirve la app con el servidor web (Nginx/Apache) apuntando a `public/`.

## Documentación

- `docs/rutas-inertia.md` — mapa completo de rutas Inertia (controlador → vista → props).
- `docs/openapi.yaml` — especificación OpenAPI de los endpoints JSON internos.
