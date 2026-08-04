import { h } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseBadge from '@/components/base/BaseBadge.vue'
import { EditIcon, TrashIcon, EyeIcon } from '@/icons'
import { formatDate } from '@/utils/date'
import { usePermissions } from './usePermissions'
import { useDialog } from './useDialog'

/** Configuración de una columna personalizada. */
interface ColumnConfig<T> {
  accessorKey: string
  header: string
  cell?: (info: { row: { original: T }; getValue: () => any }) => any
}

/** Configuración de la columna de acciones (ver/editar/eliminar). */
interface ActionsConfig<T> {
  /** Permiso requerido por defecto para las acciones. */
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

/**
 * Composable de columnas para `BaseDataTable` (TanStack Vue Table).
 *
 * Provee fábricas de columnas reutilizables: ID, campo simple, fecha,
 * columna personalizada, columna con badge y la columna de acciones
 * (ver/editar/eliminar) que respeta permisos y muestra confirmación.
 *
 * @template T Tipo de la entidad de la tabla.
 */
export function useCrudColumns<T = any>() {
  const { hasPermission } = usePermissions()
  const { confirm } = useDialog()

  /** Columna de ID. */
  function idColumn(): ColumnDef<T> {
    return {
      accessorKey: 'id',
      header: 'ID',
      cell: (info) => info.getValue(),
    }
  }

  /**
   * Columna de un campo simple.
   *
   * @param key Clave del campo en la entidad.
   * @param label Encabezado de la columna.
   * @param fallback Texto cuando el valor es nulo/vacío.
   */
  function fieldColumn(key: string, label: string, fallback = '-'): ColumnDef<T> {
    return {
      accessorKey: key,
      header: label,
      cell: (info) => info.getValue() ?? fallback,
    }
  }

  /**
   * Columna de fecha formateada (usa `formatDate`).
   *
   * @param key Clave del campo fecha.
   * @param label Encabezado de la columna.
   */
  function dateColumn(key: string, label: string): ColumnDef<T> {
    return {
      accessorKey: key,
      header: label,
      cell: (info) => formatDate(info.getValue() as string),
    }
  }

  /** Columna con celda renderizada a medida. */
  function customColumn(config: ColumnConfig<T>): ColumnDef<T> {
    return {
      accessorKey: config.accessorKey,
      header: config.header,
      cell: config.cell ?? ((info) => info.getValue()),
    }
  }

  /**
   * Columna que muestra un `BaseBadge` según el valor del campo.
   *
   * @param key Clave del campo.
   * @param label Encabezado.
   * @param colorMap Mapa `{ valor: color }` de colores del badge.
   * @param labelMap Mapa opcional `{ valor: etiqueta }`.
   */
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

  /**
   * Columna de acciones (ver/editar/eliminar).
   *
   * Cada botón se muestra solo si el usuario tiene el permiso indicado; el
   * botón de eliminar pide confirmación antes de ejecutar.
   */
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

  /**
   * Agrega la columna de acciones al final del arreglo de columnas si el
   * usuario tiene al menos un permiso de acción.
   */
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
