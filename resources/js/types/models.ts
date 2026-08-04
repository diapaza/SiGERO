/** Rol de Spatie Permission (serializado desde `App\Models\Role`). */
export interface Role {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
  /** Conteo de usuarios con este rol (agregado por `RoleController@index`). */
  users_count?: number
}

/** Permiso de Spatie Permission (serializado desde `App\Models\Permission`). */
export interface Permission {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
}

/** Categoría de objetos (serializada desde `App\Models\Categoria`). */
export interface Categoria {
  id: number
  nombre: string
  deleted_at: string | null
  created_at: string
  updated_at: string
}

/** Marca de objetos (serializada desde `App\Models\Marca`). */
export interface Marca {
  id: number
  nombre: string
  deleted_at: string | null
  created_at: string
  updated_at: string
}

/** Objeto del inventario (serializado desde `App\Models\Objeto`). */
export interface Objeto {
  id: number
  /** Código único de 4 o 12 dígitos. */
  codigo: string
  nombre: string
  modelo: string | null
  descripcion: string | null
  marca_id: number | null
  categoria_id: number | null
  /** Ruta relativa de la foto (`objetos/{codigo}.jpg`). */
  foto: string | null
  serie: string | null
  /** Estado derivado de los movimientos (no editable). */
  disponible: boolean
  deleted_at: string | null
  created_at: string
  updated_at: string
  marca?: Marca
  categoria?: Categoria
  /** Último movimiento (relación `ultimo_movimiento`). */
  ultimo_movimiento?: Movimiento
  /** Última salida registrada (relación `movimiento_activo`). */
  movimiento_activo?: Movimiento
}

/** Movimiento de préstamo (serializado desde `App\Models\Movimiento`). */
export interface Movimiento {
  id: number
  user_id: number
  objeto_id: number
  /** `salida` | `retorno`. */
  tipo_movimiento: 'salida' | 'retorno'
  fecha_hora: string
  deleted_at: string | null
  created_at: string
  updated_at: string
  objeto?: Objeto
  /** Usuario responsable del objeto en ese movimiento. */
  user?: User
  /** Usuario que registró el movimiento. */
  registrado_por?: User
}

/** Préstamo pendiente de devolución (usado en el perfil). */
export interface PendingReturn {
  id: number
  objeto_id: number
  user_id: number
  tipo_movimiento: 'salida'
  fecha_hora: string
  objeto?: Objeto
}

/** Usuario del sistema (serializado desde `App\Models\User`). */
export interface User {
  id: number
  username: string
  /** DNI de 8 dígitos. */
  dni: string
  nombres: string
  apellidos: string
  whatsapp_number: string | null
  /** Nombre completo (accesor `nombres + apellidos`). */
  name: string
  roles: Role[]
  /** Permisos efectivos del usuario (roles + directos). */
  all_permissions?: Permission[]
  /** Nombres de permisos derivados únicamente de sus roles. */
  role_permissions?: string[]
  deleted_at: string | null
  created_at: string
  updated_at: string
}

/** Resultado paginado genérico de Laravel (`paginate()`). */
export interface Paginated<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  links: Array<{ url: string | null; label: string; active: boolean }>
  prev_page_url: string | null
  next_page_url: string | null
}

/** Notificación del sistema (shape plano usado en UI). */
export interface Notification {
  id: string
  type: 'vencida' | 'salida' | 'retorno' | 'permisos' | 'cuenta' | 'general'
  title: string
  message: string
  created_at: string
  read: boolean
}

/** Grupo de ítems del menú lateral (ver `constants/menu.ts`). */
export interface MenuGroup {
  title: string
  items: Array<{
    icon?: unknown
    name: string
    path?: string
    /** Permiso requerido para mostrar el ítem. */
    permission?: string
    subItems?: Array<{
      name: string
      path: string
      new?: boolean
      pro?: boolean
    }>
  }>
}

/** Estadísticas globales del dashboard (ver `Objeto::estadisticas()`). */
export interface Estadisticas {
  total: number
  disponibles: number
  prestados: number
  eliminados: number
}

/** Movimientos agrupados por año/mes/tipo (gráfico del dashboard). */
export interface MovimientosPorMes {
  anio: number
  mes: number
  tipo_movimiento: 'salida' | 'retorno'
  total: number
}

/** Conteo de objetos por categoría (gráfico del dashboard). */
export interface ObjetosPorCategoria {
  nombre: string
  total: number
}
