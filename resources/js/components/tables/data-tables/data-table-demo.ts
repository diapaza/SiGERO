import { h } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'

export interface User {
  name: string
  email: string
  role: string
  status: 'Active' | 'Pending' | 'Cancel'
  registered: string
  avatar: string
}

const variants: Record<string, string> = {
  Active: 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
  Pending: 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
  Cancel: 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500',
}

export const columns: ColumnDef<User>[] = [
  {
    accessorKey: 'name',
    header: 'Name',
    cell: ({ row }) =>
      h('div', { class: 'flex items-center gap-3' }, [
        h('img', { class: 'w-10 h-10 rounded-full', src: row.original.avatar, alt: row.original.name }),
        h('div', [
          h('span', { class: 'block font-medium text-gray-800 text-theme-sm dark:text-white/90' }, row.original.name),
          h('span', { class: 'block text-gray-500 text-theme-xs dark:text-gray-400' }, row.original.email),
        ]),
      ]),
  },
  {
    accessorKey: 'role',
    header: 'Role',
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => {
      const status = row.original.status
      return h('span', { class: `rounded-full px-2 py-0.5 text-theme-xs font-medium ${variants[status]}` }, status)
    },
  },
  {
    accessorKey: 'registered',
    header: 'Registered',
  },
]
