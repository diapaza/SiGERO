<template>
  <TrashedEntities
    :entities="categorias"
    route-prefix="categorias"
    entity-label="categoría"
    page-title="Categorías Eliminadas"
    card-title="Categorías Eliminadas"
    card-desc="Lista de categorías que han sido eliminadas. Puede restaurarlas para volver a estar activas."
    back-label="Volver a Categorías"
    search-placeholder="Buscar categorías eliminadas..."
    empty-message="No hay categorías eliminadas."
    :columns="columns"
    :search-fields="['nombre']"
  />
</template>

/** * Página de papelera de Categorías. * * Vista renderizada por `CategoriaController@trashed`.
Muestra las categorías * eliminadas y permite restaurarlas (`categorias.restore`). * * Props
Inertia: `categorias`, `flash`. */
<script setup lang="ts">
import { computed, h } from 'vue'
import { router } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import TrashedEntities from '@/components/shared/TrashedEntities.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { RefreshIcon } from '@/icons'
import { useDialog } from '@/composables/useDialog'
import { useFlashMessages } from '@/composables/useFlashMessages'
import type { Categoria } from '@/types/models'
import { formatDate } from '@/utils/date'

const { pageProps } = useFlashMessages()
const { confirm } = useDialog()

const categorias = computed<Categoria[]>(() => pageProps.value.categorias ?? [])

const restoreCategoria = async (categoria: Categoria) => {
  const confirmed = await confirm({
    title: 'Restaurar categoría',
    description: `¿Estás seguro de restaurar la categoría "${categoria.nombre}"?`,
    icon: 'question',
    confirmLabel: 'Restaurar',
    destructive: false,
  })

  if (confirmed) {
    router.post(route('categorias.restore', categoria.id))
  }
}

const columns = computed<ColumnDef<Categoria>[]>(() => [
  {
    accessorKey: 'id',
    header: 'ID',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'nombre',
    header: 'Nombre',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'deleted_at',
    header: 'Fecha Eliminación',
    cell: (info) => formatDate(info.getValue() as string),
  },
  {
    id: 'acciones',
    header: 'Acciones',
    cell: (info) => {
      const categoria = info.row.original
      return h(
        BaseButton,
        {
          variant: 'ghost',
          size: 'sm',
          onClick: () => restoreCategoria(categoria),
          class: 'text-gray-500 hover:text-green-700',
        },
        () => h(RefreshIcon, { size: 16 }),
      )
    },
  },
])
</script>
