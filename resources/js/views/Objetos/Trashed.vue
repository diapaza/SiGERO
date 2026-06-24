<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Objetos Eliminados"
        desc="Lista de objetos que han sido eliminados. Puede restaurarlos para volver a estar activos."
      >
        <template #header>
          <BaseButton variant="outline" size="sm" :start-icon="ChevronLeftIcon" @click="goBack">
            Volver a Objetos
          </BaseButton>
        </template>

        <div class="mb-4">
          <BaseInput
            v-model="search"
            placeholder="Buscar objetos eliminados..."
            class-name="max-w-sm"
          />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredObjetos"
          :global-filter="search"
          :page-size="10"
        />

        <div
          v-if="filteredObjetos.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          No hay objetos eliminados.
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, h, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import { ChevronLeftIcon, RefreshIcon } from '@/icons'
import { useDialog } from '@/composables/useDialog'
import { toast } from 'vue-sonner'
import type { Objeto } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Objetos Eliminados')
const search = ref('')

const page = usePage()
const { confirm } = useDialog()

const pageProps = computed(() => page.props as any)
const objetos = computed<Objeto[]>(() => pageProps.value.objetos ?? [])

const filteredObjetos = computed(() => {
  if (!search.value) return objetos.value
  const term = search.value.toLowerCase()
  return objetos.value.filter(
    (obj) => obj.codigo.toLowerCase().includes(term) || obj.nombre.toLowerCase().includes(term),
  )
})

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

const goBack = () => {
  router.get(route('objetos.index'))
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

watch(
  () => pageProps.value.flash?.success,
  (message) => {
    if (message) toast.success(message)
  },
)

watch(
  () => pageProps.value.flash?.error,
  (message) => {
    if (message) toast.error(message)
  },
)
</script>
