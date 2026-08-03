import type { MenuGroup } from '@/types'
import {
  GridIcon,
  UserGroupIcon,
  UserCheckIcon,
  TagIcon,
  CategoryListIcon,
  ArrowsHorizontal,
} from '@/icons'
import ToolsIcon from '@/icons/ToolsIcon.vue'

export const menuGroups: MenuGroup[] = [
  {
    title: 'Menu',
    items: [
      {
        icon: GridIcon,
        name: 'Dashboard',
        path: '/',
        permission: 'ver dashboard',
      },
      {
        icon: UserCheckIcon,
        name: 'Roles',
        path: '/roles',
        permission: 'gestionar roles',
      },
      {
        icon: UserGroupIcon,
        name: 'Usuarios',
        path: '/users',
        permission: 'ver usuarios',
      },
      {
        icon: TagIcon,
        name: 'Marcas',
        path: '/marcas',
        permission: 'gestionar marcas',
      },
      {
        icon: CategoryListIcon,
        name: 'Categorías',
        path: '/categorias',
        permission: 'gestionar categorias',
      },
      {
        icon: ToolsIcon,
        name: 'Objetos',
        path: '/objetos',
        permission: 'gestionar objetos',
      },
      {
        icon: ArrowsHorizontal,
        name: 'Movimientos',
        path: '/movimientos',
        permission: 'registrar movimientos',
      },
    ],
  },
]
