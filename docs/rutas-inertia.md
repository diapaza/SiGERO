# Rutas Inertia — Mapa completo

Este documento describe cada ruta web de la aplicación: el **controlador** que la sirve, la **vista Vue** que renderiza y las **props** que recibe. Se mantiene alineado con `routes/web.php` y `php artisan route:list`.

> Convenciones: todas las rutas pasan por el middleware `web`; salvo las de autenticación, requieren `auth`. Cada módulo exige además el/los permiso(s) indicados (middleware `permission:...` de Spatie). Los permisos se definen en `database/seeders/PermissionSeeder.php`.

---

## Autenticación

| Método | URI       | Nombre   | Middleware                | Controlador             |
| ------ | --------- | -------- | ------------------------- | ----------------------- |
| GET    | `/signin` | `signin` | `guest`                   | `AuthController@create` |
| POST   | `/signin` | `login`  | `guest`, `throttle:login` | `AuthController@login`  |
| POST   | `/logout` | `logout` | `auth`                    | `AuthController@logout` |

### `GET /signin` — `signin`

- **Vista:** `Auth/Signin.vue`
- **Props:** ninguna (usa el formulario local de Inertia).
- **Descripción:** muestra el formulario de inicio de sesión.

### `POST /signin` — `login`

- **Controlador:** `AuthController@login` (validado con `LoginRequest`).
- **Comportamiento:** autentica por `username`/`password`, regenera la sesión y redirige a `dashboard` (o a la URL prevista). Limitado a 5 intentos/min por usuario+IP.
- **Respuesta:** redirección (no renderiza vista).

### `POST /logout` — `logout`

- **Comportamiento:** cierra sesión, invalida la sesión y redirige a `signin`.

---

## Dashboard

| Método | URI | Nombre      | Permiso         | Controlador                 |
| ------ | --- | ----------- | --------------- | --------------------------- |
| GET    | `/` | `dashboard` | `ver dashboard` | `DashboardController@index` |

- **Vista:** `Dashboard.vue`
- **Props:**
  - `estadisticas: { total, disponibles, prestados, eliminados }` — `Objeto::estadisticas()`.
  - `usuariosTotal: number`
  - `movimientosPorMes: Array<{ anio, mes, tipo_movimiento, total }>` — solo con permiso `ver reportes` (vacío en caso contrario).
  - `objetosPorCategoria: Array<{ nombre, total }>` — solo con permiso `ver reportes`.
  - `objetosPrestados: Objeto[]` — hasta 10 objetos con `disponible = false` y su `movimiento_activo` (user + registrado_por).
- **Nota:** la vista se refresca por polling cada 30 s (`usePoll`).

---

## Roles

| Método | URI             | Nombre          | Permiso           | Controlador              |
| ------ | --------------- | --------------- | ----------------- | ------------------------ |
| GET    | `/roles`        | `roles.index`   | `gestionar roles` | `RoleController@index`   |
| POST   | `/roles`        | `roles.store`   | `gestionar roles` | `RoleController@store`   |
| PUT    | `/roles/{role}` | `roles.update`  | `gestionar roles` | `RoleController@update`  |
| DELETE | `/roles/{role}` | `roles.destroy` | `gestionar roles` | `RoleController@destroy` |

- **Vista (index):** `Roles/Index.vue`
- **Props (index):** `roles` (con `users_count`), `trashedCount`, `flash`.
- **Notas:** los roles no usan soft deletes (no hay `/trashed`). `store`/`update` validan con `StoreRoleRequest`/`UpdateRoleRequest`.

---

## Usuarios

| Método | URI                         | Nombre                   | Permiso                                                            | Controlador                      |
| ------ | --------------------------- | ------------------------ | ------------------------------------------------------------------ | -------------------------------- |
| GET    | `/users`                    | `users.index`            | `ver usuarios\|crear usuarios\|editar usuarios\|eliminar usuarios` | `UserController@index`           |
| POST   | `/users`                    | `users.store`            | `crear usuarios`                                                   | `UserController@store`           |
| PUT    | `/users/{user}`             | `users.update`           | `editar usuarios`                                                  | `UserController@update`          |
| DELETE | `/users/{user}`             | `users.destroy`          | `eliminar usuarios`                                                | `UserController@destroy`         |
| GET    | `/users/trashed`            | `users.trashed`          | `editar usuarios`                                                  | `UserController@trashed`         |
| POST   | `/users/{user}/restore`     | `users.restore`          | `editar usuarios`                                                  | `UserController@restore`         |
| PUT    | `/users/{user}/permissions` | `users.permissions.sync` | `editar usuarios`                                                  | `UserController@syncPermissions` |

- **Vista (index):** `Users/Index.vue`
- **Props (index):**
  - `users: User[]` — con `roles` (y sus permisos), `all_permissions` (efectivos) y `role_permissions` (solo los de rol).
  - `roles: Role[]`
  - `trashedCount: number`
  - `allPermissions: Permission[]`
  - `flash`
- **Vista (trashed):** `Users/Trashed.vue` — props `users`, `flash`.
- **Notas:** no se puede eliminar el propio usuario ni uno con movimientos. `syncPermissions` reemplaza solo los permisos directos (los del rol no se tocan).

---

## Categorías

| Método | URI                               | Nombre               | Permiso                | Controlador                   |
| ------ | --------------------------------- | -------------------- | ---------------------- | ----------------------------- |
| GET    | `/categorias`                     | `categorias.index`   | `gestionar categorias` | `CategoriaController@index`   |
| POST   | `/categorias`                     | `categorias.store`   | `gestionar categorias` | `CategoriaController@store`   |
| PUT    | `/categorias/{categoria}`         | `categorias.update`  | `gestionar categorias` | `CategoriaController@update`  |
| DELETE | `/categorias/{categoria}`         | `categorias.destroy` | `gestionar categorias` | `CategoriaController@destroy` |
| GET    | `/categorias/trashed`             | `categorias.trashed` | `gestionar categorias` | `CategoriaController@trashed` |
| POST   | `/categorias/{categoria}/restore` | `categorias.restore` | `gestionar categorias` | `CategoriaController@restore` |

- **Vista (index):** `Categorias/Index.vue` — props `categorias`, `trashedCount`, `flash`.
- **Vista (trashed):** `Categorias/Trashed.vue` — props `categorias`, `flash`.
- **Notas:** no se puede eliminar una categoría con objetos asociados.

---

## Marcas

| Método | URI                       | Nombre           | Permiso            | Controlador               |
| ------ | ------------------------- | ---------------- | ------------------ | ------------------------- |
| GET    | `/marcas`                 | `marcas.index`   | `gestionar marcas` | `MarcaController@index`   |
| POST   | `/marcas`                 | `marcas.store`   | `gestionar marcas` | `MarcaController@store`   |
| PUT    | `/marcas/{marca}`         | `marcas.update`  | `gestionar marcas` | `MarcaController@update`  |
| DELETE | `/marcas/{marca}`         | `marcas.destroy` | `gestionar marcas` | `MarcaController@destroy` |
| GET    | `/marcas/trashed`         | `marcas.trashed` | `gestionar marcas` | `MarcaController@trashed` |
| POST   | `/marcas/{marca}/restore` | `marcas.restore` | `gestionar marcas` | `MarcaController@restore` |

- **Vista (index):** `Marcas/Index.vue` — props `marcas`, `trashedCount`, `flash`.
- **Vista (trashed):** `Marcas/Trashed.vue` — props `marcas`, `flash`.
- **Notas:** no se puede eliminar una marca con objetos asociados.

---

## Objetos

| Método | URI                         | Nombre                 | Permiso             | Controlador                    |
| ------ | --------------------------- | ---------------------- | ------------------- | ------------------------------ |
| GET    | `/objetos`                  | `objetos.index`        | `gestionar objetos` | `ObjetoController@index`       |
| POST   | `/objetos`                  | `objetos.store`        | `gestionar objetos` | `ObjetoController@store`       |
| PUT    | `/objetos/{objeto}`         | `objetos.update`       | `gestionar objetos` | `ObjetoController@update`      |
| DELETE | `/objetos/{objeto}`         | `objetos.destroy`      | `gestionar objetos` | `ObjetoController@destroy`     |
| GET    | `/objetos/trashed`          | `objetos.trashed`      | `gestionar objetos` | `ObjetoController@trashed`     |
| POST   | `/objetos/{objeto}/restore` | `objetos.restore`      | `gestionar objetos` | `ObjetoController@restore`     |
| POST   | `/objetos/upload-image`     | `objetos.upload-image` | `gestionar objetos` | `ObjetoController@uploadImage` |
| POST   | `/objetos/delete-image`     | `objetos.delete-image` | `gestionar objetos` | `ObjetoController@deleteImage` |

- **Vista (index):** `Objetos/Index.vue`
  - Props: `objetos: Objeto[]` (con `marca` y `categoria`), `marcas`, `categorias`, `trashedCount`, `flash`.
- **Vista (trashed):** `Objetos/Trashed.vue` — props `objetos`, `flash`.
- **Notas:** el código es inmutable al editar (auto-generado al crear si está vacío). `upload-image`/`delete-image` son endpoints JSON usados por el dropzone (`BaseImageDropzone.vue`). No se puede eliminar un objeto con movimientos.

---

## Movimientos

| Método | URI                         | Nombre                | Permiso                 | Controlador                    |
| ------ | --------------------------- | --------------------- | ----------------------- | ------------------------------ |
| GET    | `/movimientos`              | `movimientos.index`   | `registrar movimientos` | `MovimientoController@index`   |
| POST   | `/movimientos`              | `movimientos.store`   | `registrar movimientos` | `MovimientoController@store`   |
| PUT    | `/movimientos/{movimiento}` | `movimientos.update`  | `registrar movimientos` | `MovimientoController@update`  |
| DELETE | `/movimientos/{movimiento}` | `movimientos.destroy` | `registrar movimientos` | `MovimientoController@destroy` |

- **Vista (index):** `Movimientos/Index.vue`
  - Props: `movimientos: Movimiento[]` (con `objeto`, `user`, `registrado_por`), `users: User[]` (para el modal de edición), `flash`.
- **Notas:** `registrado_por` se fuerza al usuario autenticado en el servidor; `objeto_id` es inmutable al editar. `MovimientoService` valida los invariantes de préstamo (HTTP 422) y recalcula `disponible`.

---

## Notificaciones

| Método | URI                                  | Nombre                   | Permiso | Controlador                      |
| ------ | ------------------------------------ | ------------------------ | ------- | -------------------------------- |
| GET    | `/notifications`                     | `notifications.index`    | `auth`  | `NotificationController@index`   |
| POST   | `/notifications/read-all`            | `notifications.read-all` | `auth`  | `NotificationController@readAll` |
| POST   | `/notifications/{notification}/read` | `notifications.read`     | `auth`  | `NotificationController@read`    |

- **Vista (index):** `Notifications/Index.vue`
  - Props: `notifications` — paginación de Laravel (`Paginated<Notification>`): `data`, `current_page`, `last_page`, `per_page`, `total`, `links`, `prev_page_url`, `next_page_url`.
- **Notas:** cada notificación se serializa como `{ id, type, title, message, created_at, read }`. `read` valida que la notificación pertenezca al usuario (403 en caso contrario).

---

## Perfil

| Método | URI                 | Nombre             | Permiso | Controlador                        |
| ------ | ------------------- | ------------------ | ------- | ---------------------------------- |
| GET    | `/profile`          | `profile`          | `auth`  | `ProfileController@show`           |
| PUT    | `/profile`          | `profile.update`   | `auth`  | `ProfileController@update`         |
| PUT    | `/profile/password` | `profile.password` | `auth`  | `ProfileController@updatePassword` |

- **Vista (show):** `Others/UserProfile.vue`
  - Props: `user` (con `roles`), `pendingReturns: PendingReturn[]` (salidas activas con `objeto.marca`/`objeto.categoria`).
- **Notas:** solo administradores pueden editar el `username`; el cambio de contraseña valida la contraseña actual.

---

## Endpoints JSON internos (búsquedas)

| Método | URI                            | Nombre               | Permiso                                       | Controlador                  |
| ------ | ------------------------------ | -------------------- | --------------------------------------------- | ---------------------------- |
| GET    | `/api/objetos/search/{codigo}` | `api.objetos.search` | `gestionar objetos` o `registrar movimientos` | `ApiController@searchObjeto` |
| GET    | `/api/users/search/{dni}`      | `api.users.search`   | `ver usuarios`                                | `ApiController@searchUser`   |

- **Respuestas:** JSON con los campos públicos del objeto/usuario; `404` si no se encuentra. Documentados en `docs/openapi.yaml`.
- **Consumo:** se usan desde `Movimientos/Index.vue` (búsqueda en tiempo real de código/DNI).

---

## Página de error

| Método | URI    | Nombre       | Permiso | Controlador                 |
| ------ | ------ | ------------ | ------- | --------------------------- |
| GET    | `/403` | `errors.403` | —       | Closure en `routes/web.php` |

- **Vista:** `Errors/FourZeroThree.vue`. Los errores 403/404 de peticiones Inertia se resuelven en `bootstrap/app.php` (vistas `Errors/FourZeroThree.vue` / `Errors/FourZeroFour.vue`); 419 y 429 redirigen.
