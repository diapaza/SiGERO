import { h } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseBadge from '@/components/base/BaseBadge.vue'
import { EditIcon, TrashIcon, EyeIcon } from '@/icons'
import { formatDate } from '@/utils/date'
import { usePermissions } from './usePermissions'
import { useDialog } from './useDialog'

interface ColumnConfig<T> {
  accessorKey: string
  header: string
  cell?: (info: { row: { original: T }; getValue: () => any }) => any
}

interface ActionsConfig<T> {
  permission: string
  edit?: { onClick: (entity: T) => void; permission?: string }
  delete?: {
    onClick: (entity: T) => void
    permission?: string
    title: string
    description: string
    displayName: (entity: T) => string
  }
  view?: { onClick: (entity: T) => void; permission?: string }
}

export function useCrudColumns<T = any>() {
  const { hasPermission } = usePermissions()
  const { confirm } = useDialog()

  function idColumn(): ColumnDef<T> {
    return {
      accessorKey: 'id',
      header: 'ID',
      cell: (info) => info.getValue(),
    }
  }

  function fieldColumn(key: string, label: string, fallback = '-'): ColumnDef<T> {
    return {
      accessorKey: key,
      header: label,
      cell: (info) => info.getValue() ?? fallback,
    }
  }

  function dateColumn(key: string, label: string): ColumnDef<T> {
    return {
      accessorKey: key,
      header: label,
      cell: (info) => formatDate(info.getValue() as string),
    }
  }

  function customColumn(config: ColumnConfig<T>): ColumnDef<T> {
    return {
      accessorKey: config.accessorKey,
      header: config.header,
      cell: config.cell ?? ((info) => info.getValue()),
    }
  }

  function badgeColumn(
    key: string,
    label: string,
    colorMap: Record<string, string>,
    labelMap?: Record<string, string>,
  ): ColumnDef<T> {
    return {
      accessorKey: key,
      header: label,
      cell: (info) => {
        const value = String(
          (info.row.original as Record<string, unknown>)?.[key] ?? '',
        ).toLowerCase()
        return h(
          BaseBadge,
          {
            color: (colorMap[value] ?? 'primary') as
              | 'primary'
              | 'success'
              | 'error'
              | 'warning'
              | 'info'
              | 'light'
              | 'dark',
            size: 'sm',
          },
          () => labelMap?.[value] ?? value,
        )
      },
    }
  }

  function actionsColumn(config: ActionsConfig<T>): ColumnDef<T> {
    const editPerm = config.edit?.permission ?? config.permission
    const deletePerm = config.delete?.permission ?? config.permission
    const viewPerm = config.view?.permission ?? config.permission

    return {
      id: 'acciones',
      header: 'Acciones',
      cell: (info) => {
        const entity = info.row.original
        const buttons: any[] = []

        if (config.view && hasPermission(viewPerm)) {
          buttons.push(
            h(
              BaseButton,
              {
                variant: 'ghost',
                size: 'sm',
                onClick: () => config.view!.onClick(entity),
                class: 'text-blue-500 hover:text-blue-700',
              },
              () => h(EyeIcon, { size: 18 }),
            ),
          )
        }

        if (config.edit && hasPermission(editPerm)) {
          buttons.push(
            h(
              BaseButton,
              {
                variant: 'ghost',
                size: 'sm',
                onClick: () => config.edit!.onClick(entity),
                class: 'text-brand-500 hover:text-yellow-700',
              },
              () => h(EditIcon, { size: 18 }),
            ),
          )
        }

        if (config.delete && hasPermission(deletePerm)) {
          buttons.push(
            h(
              BaseButton,
              {
                variant: 'ghost',
                size: 'sm',
                onClick: async () => {
                  const confirmed = await confirm({
                    title: config.delete!.title,
                    description:
                      config.delete!.description + ` "${config.delete!.displayName(entity)}"`,
                    icon: 'warning',
                    confirmLabel: 'Eliminar',
                    destructive: true,
                  })
                  if (confirmed) {
                    config.delete!.onClick(entity)
                  }
                },
                class: 'text-error-500 hover:text-red-700',
              },
              () => h(TrashIcon, { size: 18 }),
            ),
          )
        }

        return h('div', { class: 'flex items-center gap-2' }, buttons)
      },
    }
  }

  function addActionsColumn(columns: ColumnDef<T>[], config: ActionsConfig<T>): ColumnDef<T>[] {
    const editPerm = config.edit?.permission ?? config.permission
    const deletePerm = config.delete?.permission ?? config.permission
    const viewPerm = config.view?.permission ?? config.permission
    const hasAnyAction =
      (config.view && hasPermission(viewPerm)) ||
      (config.edit && hasPermission(editPerm)) ||
      (config.delete && hasPermission(deletePerm))

    if (hasAnyAction) {
      columns.push(actionsColumn(config))
    }

    return columns
  }

  return {
    hasPermission,
    idColumn,
    fieldColumn,
    dateColumn,
    customColumn,
    badgeColumn,
    actionsColumn,
    addActionsColumn,
  }
}
