import type { MenuGroup } from '@/types'
import {
  GridIcon,
  CalenderIcon,
  UserCircleIcon,
  PieChartIcon,
  PageIcon,
  TableIcon,
  ListIcon,
  PlugInIcon,
  UserGroupIcon,
  UserRolesIcon,
} from '@/icons'
import BoxCubeIcon from '@/icons/BoxCubeIcon.vue'

export const menuGroups: MenuGroup[] = [
  {
    title: 'Menu',
    items: [
      {
        icon: GridIcon,
        name: 'Dashboard',
        path: '/',
      },
      {
        icon: UserRolesIcon,
        name: 'Roles',
        path: '/roles',
      },
      {
        icon: CalenderIcon,
        name: 'Calendar',
        path: '/calendar',
      },
      {
        icon: UserCircleIcon,
        name: 'User Profile',
        path: '/profile',
      },
      {
        name: 'Forms',
        icon: ListIcon,
        subItems: [{ name: 'Form Elements', path: '/form-elements', pro: false }],
      },
      {
        name: 'Tables',
        icon: TableIcon,
        subItems: [
          { name: 'Basic Tables', path: '/basic-tables', pro: false },
          { name: 'Data Tables', path: '/data-tables', pro: false },
        ],
      },
      {
        name: 'Pages',
        icon: PageIcon,
        subItems: [
          { name: 'Blank Page', path: '/blank', pro: false },
          { name: '404 Page', path: '/error-404', pro: false },
        ],
      },
    ],
  },
  {
    title: 'Others',
    items: [
      {
        icon: PieChartIcon,
        name: 'Charts',
        subItems: [
          { name: 'Line Chart', path: '/line-chart', pro: false },
          { name: 'Bar Chart', path: '/bar-chart', pro: false },
        ],
      },
      {
        icon: BoxCubeIcon,
        name: 'Ui Elements',
        subItems: [
          { name: 'Alerts', path: '/alerts', pro: false },
          { name: 'Avatars', path: '/avatars', pro: false },
          { name: 'Badge', path: '/badge', pro: false },
          { name: 'Buttons', path: '/buttons', pro: false },
          { name: 'Images', path: '/images', pro: false },
          { name: 'Videos', path: '/videos', pro: false },
          { name: 'Modal', path: '/modal', pro: false },
          { name: 'Dialogs', path: '/dialogs', pro: false },
          { name: 'Toasts', path: '/toasts', pro: false },
        ],
      },
      {
        icon: PlugInIcon,
        name: 'Authentication',
        subItems: [
          { name: 'Signin', path: '/signin', pro: false },
          { name: 'Signup', path: '/signup', pro: false },
        ],
      },
    ],
  },
]
