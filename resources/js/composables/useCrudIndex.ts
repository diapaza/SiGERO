import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import { usePage } from '@inertiajs/vue3'
import { useDialog } from './useDialog'
import { useFlashMessages } from './useFlashMessages'
import { usePermissions } from './usePermissions'
import { useModal } from './useModal'

interface CrudIndexConfig<T> {
  entityName: string
  entityLabel: string
  routePrefix: string
  searchFields: (keyof T)[]
  createFormFields: Record<string, unknown>
}

export function useCrudIndex<T = any>(config: CrudIndexConfig<T>) {
  const page = usePage()
  const { pageProps } = useFlashMessages()
  const { hasPermission } = usePermissions()
  const { confirm } = useDialog()
  const modal = useModal()

  const search = ref('')
  const editingEntity = ref<T | null>(null)

  const form = useForm(config.createFormFields)

  const entities = computed<T[]>(() => pageProps.value[config.routePrefix] ?? [])
  const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)

  const filteredEntities = computed(() => {
    if (!search.value) return entities.value
    const term = search.value.toLowerCase()
    return entities.value.filter((entity) =>
      config.searchFields.some((field) => {
        const value = entity[field]
        return value && String(value).toLowerCase().includes(term)
      }),
    )
  })

  const openCreateModal = () => {
    editingEntity.value = null
    form.reset()
    modal.open()
  }

  const openEditModal = (entity: T) => {
    editingEntity.value = entity
    Object.keys(config.createFormFields).forEach((key) => {
      if (key in entity) {
        ;(form as Record<string, unknown>)[key] = entity[key]
      }
    })
    modal.open()
  }

  const closeModal = () => {
    modal.close()
    editingEntity.value = null
    form.reset()
    form.clearErrors()
  }

  const submitForm = (options?: {
    transform?: (data: Record<string, unknown>) => Record<string, unknown>
  }) => {
    if (editingEntity.value) {
      const data: Record<string, unknown> = {}
      Object.keys(config.createFormFields).forEach((key) => {
        data[key] = (form as Record<string, unknown>)[key]
      })

      const submitData = options?.transform ? options.transform(data) : data

      form
        .transform(() => submitData)
        .put(route(`${config.routePrefix}.update`, editingEntity.value!.id), {
          onSuccess: () => closeModal(),
        })
    } else {
      form.post(route(`${config.routePrefix}.store`), {
        onSuccess: () => closeModal(),
      })
    }
  }

  const deleteEntity = async (entity: T, displayName: string) => {
    const confirmed = await confirm({
      title: `Eliminar ${config.entityLabel}`,
      description: `¿Estás seguro de eliminar ${config.entityLabel} "${displayName}"? Esta acción no se puede deshacer.`,
      icon: 'warning',
      confirmLabel: 'Eliminar',
      destructive: true,
    })

    if (confirmed) {
      router.delete(route(`${config.routePrefix}.destroy`, (entity as Record<string, unknown>).id))
    }
  }

  const goToTrashed = () => {
    router.get(route(`${config.routePrefix}.trashed`))
  }

  const restoreEntity = async (entity: T, displayName: string) => {
    const confirmed = await confirm({
      title: `Restaurar ${config.entityLabel}`,
      description: `¿Estás seguro de restaurar ${config.entityLabel} "${displayName}"?`,
      icon: 'question',
      confirmLabel: 'Restaurar',
      destructive: false,
    })

    if (confirmed) {
      router.post(route(`${config.routePrefix}.restore`, (entity as Record<string, unknown>).id))
    }
  }

  const goBack = () => {
    router.get(route(`${config.routePrefix}.index`))
  }

  return {
    search,
    editingEntity,
    form,
    entities,
    trashedCount,
    filteredEntities,
    modal,
    hasPermission,
    pageProps,
    openCreateModal,
    openEditModal,
    closeModal,
    submitForm,
    deleteEntity,
    goToTrashed,
    restoreEntity,
    goBack,
  }
}
