# PROYECTO — SiGERO

> Documentación técnica completa del sistema. **No subir a GitHub.**

- **Nombre:** SiGERO (Gestión de Equipos y Recursos Operativos)
- **Propósito:** Control de inventario de objetos/equipos y gestión de préstamos (salidas y retornos) entre usuarios de la Subunidad de Redes y Comunicaciones de la Oficinn de Tecnologias de Informacion de la Universidad Nacional del Altiplano Puno.
- **Arquitectura:** Monolito Laravel + SPA Inertia (servidor-en-renderizado con Vue 3).
- **Última revisión:** coincide con el estado del repositorio en esta entrega.

---

## 1. Resumen del sistema

SiGERO permite a la institución:

- Registrar **objetos** (con código único de 4 o 12 dígitos, foto, marca, categoría, serie).
- Registrar **préstamos**: cada objeto tiene una disponibilidad derivada de su historial de movimientos (salida/retorno).
- Gestionar **usuarios** con **roles y permisos** (Spatie Permission).
- Ver un **dashboard** con estadísticas, gráficos y la lista de objetos actualmente prestados.
- Recibir **notificaciones** automáticas (salidas, retornos, devoluciones vencidas, permisos, cuenta creada).
- Consultar el **perfil** con los objetos pendientes de devolución por usuario.

---

## 2. Arquitectura

### 2.1 Patrón general

Flujo de una petición típica:

```text
 Navegador (Vue 3 SPA)
      │
      │  petición HTTP (Inertia)
      ▼
 Router (routes/web.php) ── middleware: web, auth, permission:...
      │
      ▼
 Controller (App\Http\Controllers)
      │  delega en un Service
      ▼
 Service (App\Services) ── lógica de negocio
      │
      ▼
 Model (App\Models) ── Eloquent / BD
      │
      ▼
 Inertia::render() ── props JSON + nombre de vista
      │
      ▼
 Vista Vue (resources/js/views) ── se monta en el SPA
```

**Principios aplicados:**

- **Controllers delgados:** la lógica de negocio vive en los `Service`s, no en los controllers.
- **`BaseCrudController` / `BaseCrudService`:** base reutilizable para CRUD simples (Categorías, Marcas). Las entidades con reglas especiales (Objetos, Movimientos, Usuarios, Roles) sobrescriben solo lo necesario.
- **Validación en dos capas:** `FormRequest` en backend + `useValidation` en frontend.
- **Inertia como "pegamento":** cada página es un componente Vue que recibe props del servidor; no hay API REST externa (salvo 2 endpoints internos de búsqueda).

### 2.2 Diagrama de capas (backend)

```text
app/
├── Console/Commands/
│   └── NotificarVencidas.php          # Genera notif. de vencidas (schedule cada 6h)
├── Enums/
│   └── TipoMovimientoEnum.php         # salida | retorno
├── Http/
│   ├── Controllers/                   # 13 controllers (incl. base)
│   ├── Middleware/HandleInertiaRequests.php   # props compartidas de Inertia
│   └── Requests/                      # FormRequests por entidad
├── Models/                            # 7 modelos Eloquent
├── Notifications/                     # 5 notificaciones + base
├── Rules/UniqueIgnoringSoftDeletes.php
├── Services/                          # 9 services (incl. base)
└── Providers/AppServiceProvider.php   # bindings de services
```

### 2.3 Diagrama del frontend

```text
resources/js/
├── app.ts            # bootstrap Inertia + Vue + Pinia + ZiggyVue
├── App.vue           # providers + toast global + dialog global
├── components/
│   ├── base/         # componentes UI reutilizables (BaseInput, BaseModal, ...)
│   ├── layout/       # AdminLayout, AppHeader, AppSidebar, AuthLayout
│   ├── shared/       # ComponentCard, UserAvatar, GlobalDialog, ...
│   └── PermissionsModal.vue
├── composables/      # useCrudIndex, useCrudColumns, useValidation, ...
├── icons/            # 38 iconos SVG
├── types/models.ts   # interfaces TypeScript de las entidades
├── utils/date.ts     # formatDate / formatDateTime
└── views/            # páginas (Dashboard, Objetos, Movimientos, Users, ...)
```

---

## 3. Stack completo

### 3.1 Backend (Composer)

| Paquete                                              | Versión | Uso                                                       |
| ---------------------------------------------------- | ------- | --------------------------------------------------------- |
| `laravel/framework`                                  | ^12.0   | Framework base                                            |
| `inertiajs/inertia-laravel`                          | \*      | Renderizado de vistas Inertia desde Laravel               |
| `spatie/laravel-permission`                          | \*      | Roles y permisos (tablas `roles`, `permissions`)          |
| `tightenco/ziggy`                                    | 2.6     | Genera la función JS `route()` desde las rutas de Laravel |
| `intervention/image`                                 | 3       | Procesamiento de fotos de objetos (redimensionar, JPEG)   |
| `laravel/tinker`                                     | ^2.10   | REPL para depuración                                      |
| dev: `pint`, `phpunit`, `sail`, `collision`, `faker` | —       | Calidad y pruebas                                         |

### 3.2 Frontend (npm)

| Paquete                                                                 | Uso                                                  |
| ----------------------------------------------------------------------- | ---------------------------------------------------- |
| `vue` ^3.5                                                              | Framework UI                                         |
| `@inertiajs/vue3` ^3.4                                                  | Adaptador Vue de Inertia                             |
| `typescript` ^6                                                         | Tipado estático                                      |
| `tailwindcss` ^4 + `@tailwindcss/vite`                                  | Estilos                                              |
| `@tanstack/vue-table` ^8                                                | Tablas con ordenamiento, filtro y paginación         |
| `pinia` ^3                                                              | Estado global (solo `index.ts`, sin stores activos)  |
| `ziggy-js` ^2.6                                                         | Función `route()` en el frontend (plugin `ZiggyVue`) |
| `vue3-apexcharts` / `apexcharts`                                        | Gráficos del dashboard                               |
| `vue-sonner`                                                            | Toasts/notificaciones visuales                       |
| `dropzone`                                                              | Subida de imágenes                                   |
| `flatpickr` + `vue-flatpickr-component`                                 | Campos de fecha/hora                                 |
| `@fullcalendar/*`                                                       | Calendario (reservado/componentes de plantilla)      |
| `swiper`, `jsvectormap`                                                 | Utilidades de la plantilla base                      |
| dev: `vite` ^7, `eslint`, `prettier`, `vue-tsc`, `husky`, `lint-staged` | Build y calidad                                      |

### 3.3 Infraestructura

- **Base de datos:** MySQL (el `.env.example` viene con SQLite para desarrollo rápido; los comentarios incluyen la configuración MySQL).
- **Sesiones / caché / colas:** driver `database` (tablas `sessions`, `cache`, `jobs`).
- **Almacenamiento:** disco local, `storage/app/public` con enlace `php artisan storage:link` (fotos en `objetos/`).

---

## 4. Estructura del proyecto

### 4.1 Backend (`app/`)

| Carpeta             | Descripción                                                              |
| ------------------- | ------------------------------------------------------------------------ |
| `Console/Commands/` | Comandos Artisan propios (`NotificarVencidas`)                           |
| `Enums/`            | `TipoMovimientoEnum` (`salida` / `retorno`)                              |
| `Http/Controllers/` | `BaseCrudController` + controllers por entidad + `ApiController`         |
| `Http/Middleware/`  | `HandleInertiaRequests` (props compartidas)                              |
| `Http/Requests/`    | `FormRequest` por operación (store/update)                               |
| `Models/`           | Eloquent: `User, Role, Permission, Objeto, Movimiento, Categoria, Marca` |
| `Notifications/`    | `BaseNotification` + 5 notificaciones                                    |
| `Rules/`            | `UniqueIgnoringSoftDeletes`                                              |
| `Services/`         | `BaseCrudService` + servicios de dominio                                 |
| `Providers/`        | `AppServiceProvider` (bindings)                                          |

### 4.2 Frontend (`resources/js/`)

| Carpeta              | Descripción                                                                                                |
| -------------------- | ---------------------------------------------------------------------------------------------------------- |
| `components/base/`   | UI: `BaseButton, BaseInput, BaseModal, BaseDataTable, BaseSelectSearch, BaseImageDropzone, BaseBadge, ...` |
| `components/layout/` | `AdminLayout, AppHeader, AppSidebar, AuthLayout` + header (`UserMenu, NotificationMenu`)                   |
| `components/shared/` | `ComponentCard, StatCard, UserAvatar, GlobalDialog, ThemeToggler, TrashedEntities, ...`                    |
| `composables/`       | Lógica reutilizable (ver sección 9)                                                                        |
| `views/`             | Páginas por módulo                                                                                         |
| `types/`             | Interfaces TS                                                                                              |
| `utils/`             | Helpers (fechas)                                                                                           |

---

## 5. Modelo de datos

### 5.1 Diagrama de entidades

```text
┌──────────┐   1      ┌──────────────┐   1     ┌────────────┐
│  marcas  │1────────<│   objetos    │>────────│ categorías │
└──────────┘          │              │         └────────────┘
                      └──────┬───────┘
                             │ 1
                      ┌──────┴───────┐
                      │  movimientos │
                      │  user_id     │──────┐
                      │registrado_por│──────┤
                      └──────────────┘      │
                                            ▼
                                      ┌───────────┐
                                      │   users   │
                                      └─────┬─────┘
                                            │ morphs (Spatie)
                         ┌──────────────────┼──────────────────┐
                         │                  │                  │
                  ┌──────┴───────┐   ┌──────┴───────┐   ┌──────┴───────┐
                  │model_has_    │   │model_has_    │   │ notifications│
                  │roles         │   │permissions   │   └──────────────┘
                  └──────┬───────┘   └──────┬───────┘
                         │                  │
                  ┌──────┴───────┐   ┌──────┴───────┐
                  │    roles    │   │  permissions │
                  └──────┬───────┘   └──────┬───────┘
                         └── role_has_permissions ──┘
```

Leyenda:

- `1──<` = relación uno-a-muchos (el lado `many` lleva la FK).
- `movimientos.user_id` → `users` (usuario **responsable** del objeto) y `movimientos.registrado_por` → `users` (usuario que **registró** el movimiento): dos FKs distintas hacia la misma tabla.
- Spatie: `users` se vincula a `roles` y `permissions` mediante relaciones polimórficas (`model_has_roles`, `model_has_permissions`); `role_has_permissions` une roles y permisos.
- `notifications` es una tabla polimórfica de Laravel (`notifiable` → `users`).

### 5.2 Tablas principales

**users**

| Campo               | Tipo                 | Notas                                         |
| ------------------- | -------------------- | --------------------------------------------- |
| id                  | bigint PK            |                                               |
| username            | string(255)          | único                                         |
| dni                 | char(8)              | único                                         |
| nombres / apellidos | string(120)          | `name` es un accessor = `nombres + apellidos` |
| whatsapp_number     | char(9)              | obligatorio, exactamente 9 dígitos            |
| password            | string               | hash (cast `hashed`)                          |
| remember_token      | string(100) nullable | autenticación de sesión                       |
| deleted_at          | timestamp            | SoftDeletes                                   |

**objetos**

| Campo                   | Tipo                  | Notas                                             |
| ----------------------- | --------------------- | ------------------------------------------------- |
| codigo                  | string(12)            | único, 4 o 12 dígitos                             |
| nombre                  | string(150)           |                                                   |
| modelo                  | string(250) nullable  |                                                   |
| descripcion             | text nullable         |                                                   |
| serie                   | string(50) nullable   |                                                   |
| marca_id / categoria_id | FK nullable           | `nullOnDelete`                                    |
| foto                    | string(2048) nullable | ruta relativa en `storage/app/public/objetos/`    |
| disponible              | boolean               | **derivado** de los movimientos (ver sección 6.3) |

**movimientos**

| Campo           | Tipo         | Notas                                                                |
| --------------- | ------------ | -------------------------------------------------------------------- |
| user_id         | FK → users   | usuario **responsable** del objeto (quién lo tiene/recibe)           |
| objeto_id       | FK → objetos | objeto involucrado (inmutable al editar)                             |
| registrado_por  | FK → users   | usuario que **registró** el movimiento                               |
| tipo_movimiento | string(20)   | `salida` \| `retorno` (enum en la capa de app: `TipoMovimientoEnum`) |
| fecha_hora      | timestamp    | indexado                                                             |

Cada movimiento registra **un** objeto; el alta se hace de a un movimiento por petición (`movimientos.store`). No existe tabla `prestamos` ni columna `prestamo_id`.

**marcas**

| Campo      | Tipo      | Notas       |
| ---------- | --------- | ----------- |
| id         | bigint PK |             |
| nombre     | string    | único       |
| deleted_at | timestamp | SoftDeletes |

**categorias**

| Campo      | Tipo      | Notas       |
| ---------- | --------- | ----------- |
| id         | bigint PK |             |
| nombre     | string    | único       |
| deleted_at | timestamp | SoftDeletes |

**roles** (Spatie Permission)

| Campo      | Tipo      | Notas                  |
| ---------- | --------- | ---------------------- |
| id         | bigint PK |                        |
| name       | string    | único con `guard_name` |
| guard_name | string    | por defecto `web`      |

Sin soft deletes (a diferencia del resto de entidades).

**permissions** (Spatie Permission)

| Campo      | Tipo      | Notas                  |
| ---------- | --------- | ---------------------- |
| id         | bigint PK |                        |
| name       | string    | único con `guard_name` |
| guard_name | string    | por defecto `web`      |

**Pivotes de Spatie**

| Tabla                   | Columnas                                  | Notas                                                 |
| ----------------------- | ----------------------------------------- | ----------------------------------------------------- |
| `role_has_permissions`  | `role_id`, `permission_id`                | PK compuesta; `cascade` on delete                     |
| `model_has_roles`       | `role_id`, `model_type`, `model_id`       | morph; PK (`role_id`, `model_id`, `model_type`)       |
| `model_has_permissions` | `permission_id`, `model_type`, `model_id` | morph; PK (`permission_id`, `model_id`, `model_type`) |

**notifications** (Laravel)

| Campo                           | Tipo               | Notas                               |
| ------------------------------- | ------------------ | ----------------------------------- |
| id                              | uuid PK            |                                     |
| type                            | string             | clase de la notificación            |
| notifiable_id / notifiable_type | morph              | → `users`                           |
| data                            | text               | payload JSON                        |
| read_at                         | timestamp nullable | índice (`notifiable_id`, `read_at`) |

**Relaciones del modelo `User`:**

- `movimientos()` — movimientos donde el usuario es el **responsable** (`user_id`).
- `movimientosRegistrados()` — movimientos **registrados** por el usuario (`registrado_por`).
- `roles`, `permissions` — vía Spatie (morph `model_has_roles` / `model_has_permissions`).

**Relaciones del modelo `Objeto`:**

- `movimientos()` — todos los movimientos.
- `ultimoMovimiento()` — último movimiento por `fecha_hora`.
- `movimientoActivo()` — última salida registrada (se usa en dashboard/notificaciones junto al flag `disponible=false`).
- `marca()`, `categoria()`.

**Relaciones del modelo `Movimiento`:**

- `objeto()`, `user()` (quién lo tiene), `registradoPor()` (quién registró).

### 5.3 Migraciones

- `0001_01_01_*` — `cache`, `jobs`.
- `2026_06_12_*` — `roles`, `marcas`, `categorias`, `users`, `objetos`, `movimientos`.
- `2026_07_06_*` — adaptación de roles a Spatie Permission, eliminación de `users.role_id`, tablas de permisos.
- `2026_08_02_*` — tabla `notifications` (estándar de Laravel, uuid).
- `2026_08_05_000000` — `users.whatsapp_number` de `string(15)` nullable a `char(9)` NOT NULL (backfill `000000000` a las filas con NULL previo).

---

## 6. Roles, permisos y lógica de negocio

### 6.1 Roles (3)

| Rol               | Permisos                                                                                                           |
| ----------------- | ------------------------------------------------------------------------------------------------------------------ |
| **Administrador** | Todos (12)                                                                                                         |
| **Personal**      | `ver dashboard`, `ver perfil propio`, `ver reportes`, `ver usuarios`, `gestionar objetos`, `registrar movimientos` |
| **Practicante**   | `ver dashboard`, `ver perfil propio`                                                                               |

### 6.2 Permisos (12)

```text
ver dashboard · ver perfil propio · ver reportes · gestionar roles
ver usuarios · crear usuarios · editar usuarios · eliminar usuarios
gestionar categorias · gestionar marcas · gestionar objetos · registrar movimientos
```

### 6.3 Lógica clave: disponibilidad de objetos

- El campo `disponible` **no se edita manualmente**; se **deriva del historial** de movimientos.
- Al crear/actualizar/eliminar/restaurar un movimiento, `MovimientoService` recalcula la disponibilidad:

```text
disponible = (último movimiento NO es 'salida')
```

- **Invariantes de préstamo** (validados en `MovimientoService::assertTipoValido`):
  - No se permite una **salida** si el objeto ya está prestado.
  - No se permite un **retorno** si el objeto no está prestado.
  - El sistema responde HTTP 422 si se violan.

### 6.4 Códigos de objeto

- El código es de **4 o 12 dígitos** numéricos.
- Si se deja vacío al crear, `Objeto::generarSiguienteCodigo()` genera el siguiente `0001..9999` (relleno a 4 con ceros a la izquierda).

### 6.5 Notificaciones (5 tipos)

| Tipo       | Cuándo                                                                         | Destinatarios                                                  |
| ---------- | ------------------------------------------------------------------------------ | -------------------------------------------------------------- |
| `salida`   | Al registrar un movimiento `salida`                                            | Operadores (permiso `registrar movimientos` o `ver dashboard`) |
| `retorno`  | Al registrar un movimiento `retorno`                                           | Operadores                                                     |
| `vencida`  | Devoluciones > 3 días sin retorno (comando `app:notificar-vencidas`, cada 6 h) | El responsable del objeto + operadores                         |
| `permisos` | Cuando cambian los roles/permisos de un usuario                                | El usuario afectado                                            |
| `cuenta`   | Al crear un usuario                                                            | El usuario nuevo                                               |

Las notificaciones usan el sistema nativo de Laravel (tabla `notifications`, driver `database`). El frontend las consume vía props compartidas de Inertia y las marca como leídas al abrir el menú.

---

## 7. Módulos y funcionalidades

### 7.1 Dashboard (`/`)

- Tarjetas: total, disponibles, prestados, usuarios.
- Gráficos (permiso `ver reportes`): movimientos por mes (barras), objetos por categoría (pie).
- Tabla "Objetos en Préstamo": últimos 10 objetos `disponible=false` con persona, teléfono y fecha de salida (vía `movimientoActivo`).
- **Polling:** se refresca cada 30 s con `usePoll` (solo las props del dashboard).

### 7.2 Objetos (`/objetos`)

- CRUD con imagen, marca/categoría (creables desde el formulario), serie, descripción.
- Papelera (`/objetos/trashed`) con restauración.
- Foto: subida por dropzone, procesada (redimensionada a máx. 800 px, JPEG) y renombrada a `{codigo}.jpg`.
- La disponibilidad se muestra como badge informativo (no editable).

### 7.3 Movimientos (`/movimientos`)

- Registro de **un movimiento por vez** (`POST movimientos.store`):
  1. Buscar el objeto por código (4 o 12 dígitos, auto-búsqueda al llegar a la longitud esperada). El tipo se auto-determina por la disponibilidad: **Salida** si el objeto está disponible, **Retorno** si está prestado; el sistema bloquea salidas sobre objetos ya prestados y retornos sobre objetos disponibles.
  2. Buscar al responsable por DNI (auto-búsqueda a 8 dígitos) y asociarlo al movimiento.
  3. Enviar el formulario; el servidor fuerza `registrado_por` al usuario autenticado y valida los invariantes de préstamo (HTTP 422 si se violan).
- Edición y eliminación de movimientos individuales (recalculan disponibilidad).
- Lista con tabla ordenable/filtrable y badges de tipo (Salida=ámbar, Retorno=verde).

### 7.4 Usuarios (`/users`)

- CRUD con roles, contraseña auto-generada (o editable), búsqueda por DNI.
- **Permisos directos** por usuario (`PermissionsModal`).
- Papelera con restauración.

### 7.5 Roles, Marcas, Categorías

- CRUD simples basados en `BaseCrudController`.
- Marcas/Categorías con papelera y restauración; Roles sin soft-delete.

### 7.6 Notificaciones (`/notifications`)

- Menú de campana en el header (últimas 10 con scroll; marca todo como leído al abrir).
- Página "Ver todas" con paginación (10 por página).

### 7.7 Perfil (`/profile`)

- Datos personales (el administrador puede editar el username), cambio de contraseña.
- Pestaña "Objetos Pendientes": préstamos activos del usuario (última salida sin retorno posterior).

---

## 8. Rutas

Todas las rutas web viven en `routes/web.php`. Resumen:

| Método              | URI                                              | Nombre                                         | Permiso                                       |
| ------------------- | ------------------------------------------------ | ---------------------------------------------- | --------------------------------------------- |
| GET                 | `/signin`                                        | `signin`                                       | guest                                         |
| POST                | `/signin`                                        | `login`                                        | guest                                         |
| POST                | `/logout`                                        | `logout`                                       | auth                                          |
| GET/PUT             | `/profile`                                       | `profile`, `profile.update`                    | auth                                          |
| PUT                 | `/profile/password`                              | `profile.password`                             | auth                                          |
| GET                 | `/notifications`                                 | `notifications.index`                          | auth                                          |
| POST                | `/notifications/read-all`                        | `notifications.read-all`                       | auth                                          |
| POST                | `/notifications/{n}/read`                        | `notifications.read`                           | auth                                          |
| GET                 | `/`                                              | `dashboard`                                    | `ver dashboard`                               |
| GET/POST/PUT/DELETE | `/roles...`                                      | `roles.*`                                      | `gestionar roles`                             |
| GET/POST/PUT/DELETE | `/users...` (+ trashed/restore/permissions)      | `users.*`                                      | `ver/crear/editar/eliminar usuarios`          |
| GET/POST/PUT/DELETE | `/categorias...` (+ trashed/restore)             | `categorias.*`                                 | `gestionar categorias`                        |
| GET/POST/PUT/DELETE | `/marcas...` (+ trashed/restore)                 | `marcas.*`                                     | `gestionar marcas`                            |
| GET/POST/PUT/DELETE | `/objetos...` (+ trashed/restore)                | `objetos.*`                                    | `gestionar objetos`                           |
| POST                | `/objetos/upload-image`, `/objetos/delete-image` | `objetos.upload-image`, `objetos.delete-image` | `gestionar objetos`                           |
| GET/POST/PUT/DELETE | `/movimientos...`                                | `movimientos.*`                                | `registrar movimientos`                       |
| GET                 | `/api/objetos/search/{codigo}`                   | `api.objetos.search`                           | `gestionar objetos` o `registrar movimientos` |
| GET                 | `/api/users/search/{dni}`                        | `api.users.search`                             | `ver usuarios`                                |
| GET                 | `/403`                                           | `errors.403`                                   | —                                             |

---

## 9. Frontend

### 9.1 Bootstrap (`app.ts`)

```text
createInertiaApp
  ├─ resolve: import.meta.glob('./views/**/*.vue')   # carga todas las vistas
  ├─ setup: createApp → use(pinia) → use(VueApexCharts) → use(Inertia plugin) → use(ZiggyVue)
  └─ progress bar de Inertia
```

`ZiggyVue` expone `route()` a todos los templates; `@routes` en el blade provee el JSON de rutas.

### 9.2 Componentes base (`components/base/`)

`BaseButton, BaseInput, BaseTextarea, BasePasswordInput, BaseFormField, BaseSelectSearch, BaseCheckbox, BaseModal, BaseDataTable, BaseBadge, BaseCard, BaseChart, BaseCloseButton, BaseImageDropzone, BaseToast`.

### 9.3 Composables

| Composable                                                                | Uso                                                                  |
| ------------------------------------------------------------------------- | -------------------------------------------------------------------- |
| `useCrudIndex`                                                            | Estado CRUD genérico (búsqueda, modal, form, submit, delete)         |
| `useCrudColumns`                                                          | Columnas de tabla reutilizables (id, campo, fecha, badge, acciones)  |
| `useValidation`                                                           | Validación en frontend (reglas por entidad)                          |
| `usePermissions`                                                          | `hasPermission` / `hasAnyPermission` / `hasAllPermissions`           |
| `useDialog`                                                               | Diálogos de confirmación globales                                    |
| `useModal`                                                                | Estado de modales                                                    |
| `useFlashMessages`                                                        | Acceso a `flash` de Inertia (los toasts se disparan desde `App.vue`) |
| `useClickOutside`, `useSelect`, `useSidebar`, `useTheme`, `useUserAvatar` | Utilidades                                                           |

### 9.4 Props compartidas (Inertia)

En `HandleInertiaRequests` se comparten:

- `auth.user` (id, username, name, roles, permissions)
- `notifications` (últimas 10, con `read`)
- `unreadNotifications` (contador)
- `errors`, `flash`, `appName`

---

## 10. Servicios (`app/Services/`)

| Service                             | Responsabilidad                                                                      |
| ----------------------------------- | ------------------------------------------------------------------------------------ |
| `BaseCrudService`                   | CRUD base (create/update/delete/restore)                                             |
| `ObjetoService`                     | Crear/editar objeto + renombrar imagen según código                                  |
| `MovimientoService`                 | Crear/editar/eliminar/restaurar movimientos + **invariantes + derivar `disponible`** |
| `UserService`                       | CRUD usuarios, roles, permisos, contraseña, perfil                                   |
| `RoleService`                       | CRUD roles (detach permisos al eliminar)                                             |
| `CategoriaService` / `MarcaService` | CRUD con dependencias (no borrar si tiene objetos)                                   |
| `NotificationService`               | Disparo de las 5 notificaciones + `generarVencidas()`                                |
| `ImageService`                      | Procesar/renombrar/eliminar imágenes (con protección anti path-traversal)            |

---

## 11. Operación y despliegue

### 11.1 Programación de tareas (schedule)

En `routes/console.php`:

```php
Schedule::command('app:notificar-vencidas')->everySixHours();
```

Para que se ejecute en producción, el cron del servidor debe llamar a `php artisan schedule:run` cada minuto.

### 11.2 Colas

`QUEUE_CONNECTION=database`. No se usan colas para las notificaciones actualmente (se envían en línea), pero la infraestructura (tabla `jobs`) está lista.

### 11.3 Almacenamiento

- Crear el enlace: `php artisan storage:link`.
- Las fotos se guardan en `storage/app/public/objetos/`.

### 11.4 Calidad

```bash
php artisan test        # pruebas
npm run typecheck       # tipos TS
npm run lint            # ESLint
npm run format          # Prettier
```

---

## 12. Notas de seguridad

- Las rutas de administración están protegidas por middleware `permission:...`.
- La subida/borrado de imágenes valida el tipo y previene path traversal.
- `delete-image` solo permite borrar archivos dentro de `storage/app/public/objetos/`.
- Las contraseñas se guardan con hash (cast `hashed`).
- Los permisos se validan tanto en backend (middleware) como en frontend (renderizado condicional).
