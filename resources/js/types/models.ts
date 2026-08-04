export interface Role {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
  users_count?: number
}

export interface Permission {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
}

export interface Categoria {
  id: number
  nombre: string
  deleted_at: string | null
  created_at: string
  updated_at: string
}

export interface Marca {
  id: number
  nombre: string
  deleted_at: string | null
  created_at: string
  updated_at: string
}

export interface Objeto {
  id: number
  codigo: string
  nombre: string
  modelo: string | null
  descripcion: string | null
  marca_id: number | null
  categoria_id: number | null
  foto: string | null
  serie: string | null
  disponible: boolean
  deleted_at: string | null
  created_at: string
  updated_at: string
  marca?: Marca
  categoria?: Categoria
  ultimo_movimiento?: Movimiento
  movimiento_activo?: Movimiento
}

export interface Movimiento {
  id: number
  user_id: number
  objeto_id: number
  tipo_movimiento: 'salida' | 'retorno'
  fecha_hora: string
  deleted_at: string | null
  created_at: string
  updated_at: string
  objeto?: Objeto
  user?: User
  registrado_por?: User
}

export interface PendingReturn {
  id: number
  objeto_id: number
  user_id: number
  tipo_movimiento: 'salida'
  fecha_hora: string
  objeto?: Objeto
}

export interface User {
  id: number
  username: string
  dni: string
  nombres: string
  apellidos: string
  whatsapp_number: string | null
  name: string
  roles: Role[]
  all_permissions?: Permission[]
  role_permissions?: string[]
  deleted_at: string | null
  created_at: string
  updated_at: string
}

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

export interface Notification {
  id: string
  type: 'vencida' | 'salida' | 'retorno' | 'permisos' | 'cuenta' | 'general'
  title: string
  message: string
  created_at: string
  read: boolean
}

export interface MenuGroup {
  title: string
  items: Array<{
    icon?: unknown
    name: string
    path?: string
    permission?: string
    subItems?: Array<{
      name: string
      path: string
      new?: boolean
      pro?: boolean
    }>
  }>
}

export interface Estadisticas {
  total: number
  disponibles: number
  prestados: number
  eliminados: number
}

export interface MovimientosPorMes {
  anio: number
  mes: number
  tipo_movimiento: 'salida' | 'retorno'
  total: number
}

export interface ObjetosPorCategoria {
  nombre: string
  total: number
}
