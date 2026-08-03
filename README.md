# SiGERO

Sistema de Gestión de Objetos y Equipos con control de préstamos (salidas y retornos) para instituciones. Permite administrar inventario, registrar movimientos de préstamo, gestionar usuarios con roles y permisos, y mantener notificaciones automáticas de devoluciones vencidas.

## Stack

| Capa                     | Tecnología                             |
| ------------------------ | -------------------------------------- |
| Backend                  | Laravel 12 (PHP 8.2)                   |
| Frontend                 | Vue 3 + TypeScript + Inertia.js 3      |
| Estilos                  | Tailwind CSS 4                         |
| Base de datos            | MySQL (compatible con SQLite en local) |
| Autenticación / permisos | Spatie Laravel Permission              |
| Tablas                   | TanStack Vue Table 8                   |
| Gráficos                 | ApexCharts                             |

## Requisitos

- PHP ≥ 8.2
- Composer
- Node.js ≥ 20 y npm
- MySQL (o SQLite para pruebas locales)
- Extensiones PHP: gd (procesamiento de imágenes), pdo_mysql

## Instalación

1. Clonar o copiar el proyecto e instalar dependencias PHP:

   ```bash
   composer install
   ```

2. Crear el archivo de entorno:

   ```bash
   cp .env.example .env
   ```

3. Configurar en `.env` la conexión a la base de datos (MySQL):

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sigero
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Generar la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

5. Ejecutar las migraciones y los seeders (crea roles, permisos y el usuario administrador):

   ```bash
   php artisan migrate --seed
   ```

6. Crear el enlace de almacenamiento público (fotos de objetos):

   ```bash
   php artisan storage:link
   ```

7. Instalar y compilar las dependencias del frontend:

   ```bash
   npm install
   npm run build
   ```

> **Atajo:** el proyecto incluye el comando `composer setup`, que ejecuta los pasos 1 a 7 automáticamente (menos la configuración de la base de datos en `.env`).

## Desarrollo

Para desarrollo local, abre dos terminales:

- Terminal 1 — servidor Laravel:

  ```bash
  php artisan serve
  ```

- Terminal 2 — compilador de frontend con recarga en vivo:

  ```bash
  npm run dev
  ```

Alternativamente, usa `composer dev` para levantar servidor, colas, logs y Vite a la vez.

## Credenciales iniciales

| Usuario | Contraseña  | Rol           |
| ------- | ----------- | ------------- |
| `admin` | `Admin123$` | Administrador |

> Cambia la contraseña del usuario administrador tras el primer inicio de sesión.

## Comandos útiles

| Comando                              | Descripción                                                          |
| ------------------------------------ | -------------------------------------------------------------------- |
| `php artisan test`                   | Ejecuta las pruebas                                                  |
| `npm run lint`                       | Lint de ESLint sobre `resources/js`                                  |
| `npm run typecheck`                  | Verificación de tipos con `vue-tsc`                                  |
| `npm run format`                     | Formatea el código con Prettier                                      |
| `npm run build`                      | Compila el frontend para producción                                  |
| `php artisan app:notificar-vencidas` | Genera notificaciones de devoluciones vencidas (programado cada 6 h) |

## Documentación

La documentación técnica completa del sistema se encuentra en `PROYECTO.md`.
