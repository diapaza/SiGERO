import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useDialog } from './useDialog'
import { useFlashMessages } from './useFlashMessages'
import { usePermissions } from './usePermissions'
import { useModal } from './useModal'

/**
 * Configuración del composable de CRUD.
 *
 * @template T Tipo de la entidad gestionada.
 */
interface CrudIndexConfig<T> {
  /** Nombre lógico de la entidad (para nombres de ruta y mensajes). */
  entityName: string
  /** Etiqueta en singular usada en los diálogos de confirmación. */
  entityLabel: string
  /** Prefijo de las rutas (`{routePrefix}.index`, `.store`, ...). */
  routePrefix: string
  /** Campos usados para filtrar la tabla localmente. */
  searchFields: (keyof T)[]
  /** Valores iniciales del formulario (y claves que se envían al servidor). */
  createFormFields: Record<string, any>
}

/**
 * Composable que centraliza el estado y las acciones de un CRUD de listado.
 *
 * Expone el formulario (Inertia `useForm`), la búsqueda, el filtrado local,
 * el estado del modal de crear/editar, la papelera y los diálogos de
 * confirmación de eliminar/restaurar. Es la base de las vistas de
 * Categorías, Marcas, Roles, Usuarios y Objetos.
 *
 * @template T Tipo de la entidad gestionada.
 */
export function useCrudIndex<T = any>(config: CrudIndexConfig<T>) {
  const { pageProps } = useFlashMessages()
  const { hasPermission } = usePermissions()
  const { confirm } = useDialog()
  const modal = useModal()

  /** Término de búsqueda para filtrar la tabla. */
  const search = ref('')
  /** Entidad en edición (`null` si se está creando). */
  const editingEntity = ref<T | null>(null)

  /** Formulario Inertia con los campos definidos en la configuración. */
  const form = useForm(config.createFormFields)

  /** Entidades del listado (prop `{routePrefix}` de la página). */
  const entities = computed<T[]>(() => pageProps.value[config.routePrefix] ?? [])
  /** Número de registros en papelera (prop `trashedCount`). */
  const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)

  /** Entidades filtradas localmente por el término de búsqueda. */
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

  /** Abre el modal en modo "crear" y resetea el formulario. */
  const openCreateModal = () => {
    editingEntity.value = null
    form.reset()
    modal.open()
  }

  /** Abre el modal en modo "editar" precargando el formulario con la entidad. */
  const openEditModal = (entity: T) => {
    editingEntity.value = entity
    const entityRecord = entity as Record<string, any>
    Object.keys(config.createFormFields).forEach((key) => {
      if (key in entityRecord) {
        ;(form as Record<string, any>)[key] = entityRecord[key]
      }
    })
    modal.open()
  }

  /** Cierra el modal y resetea el formulario y sus errores. */
  const closeModal = () => {
    modal.close()
    editingEntity.value = null
    form.reset()
    form.clearErrors()
  }

  /**
   * Envía el formulario (POST al crear, PUT al editar).
   *
   * @param options.transform Callback opcional para transformar los datos antes de enviarlos.
   */
  const submitForm = (options?: {
    transform?: (data: Record<string, unknown>) => Record<string, unknown>
  }) => {
    if (editingEntity.value) {
      const data: Record<string, any> = {}
      Object.keys(config.createFormFields).forEach((key) => {
        data[key] = (form as Record<string, any>)[key]
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

  /**
   * Elimina una entidad tras confirmar en el diálogo global.
   *
   * @param entity Entidad a eliminar.
   * @param displayName Nombre mostrado en la confirmación.
   */
  const deleteEntity = async (entity: T, displayName: string) => {
    const confirmed = await confirm({
      title: `Eliminar ${config.entityLabel}`,
      description: `¿Estás seguro de eliminar ${config.entityLabel} "${displayName}"? Esta acción no se puede deshacer.`,
      icon: 'warning',
      confirmLabel: 'Eliminar',
      destructive: true,
    })

    if (confirmed) {
      router.delete(route(`${config.routePrefix}.destroy`, (entity as { id: string | number }).id))
    }
  }

  /** Navega a la papelera (`{routePrefix}.trashed`). */
  const goToTrashed = () => {
    router.get(route(`${config.routePrefix}.trashed`))
  }

  /**
   * Restaura una entidad eliminada tras confirmar en el diálogo global.
   *
   * @param entity Entidad a restaurar.
   * @param displayName Nombre mostrado en la confirmación.
   */
  const restoreEntity = async (entity: T, displayName: string) => {
    const confirmed = await confirm({
      title: `Restaurar ${config.entityLabel}`,
      description: `¿Estás seguro de restaurar ${config.entityLabel} "${displayName}"?`,
      icon: 'question',
      confirmLabel: 'Restaurar',
      destructive: false,
    })

    if (confirmed) {
      router.post(route(`${config.routePrefix}.restore`, (entity as { id: string | number }).id))
    }
  }

  /** Vuelve al listado principal (`{routePrefix}.index`). */
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
