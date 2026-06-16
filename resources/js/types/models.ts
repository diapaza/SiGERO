export interface Role {
  id: number
  nombre: string
  deleted_at: string | null
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
}

export interface Movimiento {
  id: number
  user_id: number
  objeto_id: number
  registrado_por: number
  tipo_movimiento: 'salida' | 'retorno'
  fecha_hora: string
  deleted_at: string | null
  created_at: string
  updated_at: string
  objeto?: Objeto
  user?: User
  registradoPor?: User
}

export interface User {
  id: number
  username: string
  dni: string
  nombres: string
  apellidos: string
  whatsapp_number: string | null
  role_id: number | null
  name: string
  role?: Role
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
}

export interface Product {
  id: number
  name: string
  variants: number
  image: string
  category: string
  price: string
  status: 'Delivered' | 'Pending' | 'Canceled'
}

export interface Notification {
  id: number
  userName: string
  userImage: string
  action: string
  project: string
  type: string
  time: string
  status: 'online' | 'offline'
}

export interface TeamMember {
  name: string
  role: string
  avatar: string
  project: string
  team: string[]
  status: 'Active' | 'Pending' | 'Cancel'
  budget: string
}

export interface CalendarEvent {
  id: number
  title: string
  start: Date
  end?: Date
  color: string
  description?: string
}

export interface MenuGroup {
  title: string
  items: Array<{
    icon?: unknown
    name: string
    path?: string
    subItems?: Array<{
      name: string
      path: string
      new?: boolean
      pro?: boolean
    }>
  }>
}
