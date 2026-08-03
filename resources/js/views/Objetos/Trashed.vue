<template>
  <TrashedEntities
    :entities="objetos"
    route-prefix="objetos"
    entity-label="objeto"
    page-title="Objetos Eliminados"
    card-title="Objetos Eliminados"
    card-desc="Lista de objetos que han sido eliminados. Puede restaurarlos para volver a estar activos."
    back-label="Volver a Objetos"
    search-placeholder="Buscar objetos eliminados..."
    empty-message="No hay objetos eliminados."
    :columns="columns"
    :search-fields="['codigo', 'nombre']"
  />
</template>

<script setup lang="ts">
import { computed, h } from 'vue'
import { router } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import TrashedEntities from '@/components/shared/TrashedEntities.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { RefreshIcon } from '@/icons'
import { useDialog } from '@/composables/useDialog'
import { useFlashMessages } from '@/composables/useFlashMessages'
import type { Objeto } from '@/types/models'
import { formatDate } from '@/utils/date'

const { pageProps } = useFlashMessages()
const { confirm } = useDialog()

const objetos = computed<Objeto[]>(() => pageProps.value.objetos ?? [])

const restoreObjeto = async (objeto: Objeto) => {
  const confirmed = await confirm({
    title: 'Restaurar objeto',
    description: `¿Estás seguro de restaurar el objeto "${objeto.nombre}"?`,
    icon: 'question',
    confirmLabel: 'Restaurar',
    destructive: false,
  })

  if (confirmed) {
    router.post(route('objetos.restore', objeto.id))
  }
}

const columns = computed<ColumnDef<Objeto>[]>(() => [
  {
    accessorKey: 'codigo',
    header: 'Código',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'nombre',
    header: 'Nombre',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'marca',
    header: 'Marca',
    cell: (info) => {
      const objeto = info.row.original
      return objeto.marca?.nombre ?? '-'
    },
  },
  {
    accessorKey: 'categoria',
    header: 'Categoría',
    cell: (info) => {
      const objeto = info.row.original
      return objeto.categoria?.nombre ?? '-'
    },
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
      const objeto = info.row.original
      return h(
        BaseButton,
        {
          variant: 'ghost',
          size: 'sm',
          onClick: () => restoreObjeto(objeto),
          class: 'text-gray-500 hover:text-green-700',
        },
        () => h(RefreshIcon, { size: 16 }),
      )
    },
  },
])
</script>
