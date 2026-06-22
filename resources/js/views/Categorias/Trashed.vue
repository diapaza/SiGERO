<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Categorías Eliminadas"
        desc="Lista de categorías que han sido eliminadas. Puede restaurarlas para volver a estar activas."
      >
        <template #header>
          <BaseButton variant="outline" size="sm" :start-icon="ChevronLeftIcon" @click="goBack">
            Volver a Categorías
          </BaseButton>
        </template>

        <div class="mb-4">
          <BaseInput
            v-model="search"
            placeholder="Buscar categorías eliminadas..."
            class-name="max-w-sm"
          />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredCategorias"
          :global-filter="search"
          :page-size="10"
        />

        <div
          v-if="filteredCategorias.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          No hay categorías eliminadas.
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
import type { Categoria } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Categorías Eliminadas')
const search = ref('')

const page = usePage()
const { confirm } = useDialog()

const pageProps = computed(() => page.props as any)
const categorias = computed<Categoria[]>(() => pageProps.value.categorias ?? [])

const filteredCategorias = computed(() => {
  if (!search.value) return categorias.value
  const term = search.value.toLowerCase()
  return categorias.value.filter((categoria) => categoria.nombre.toLowerCase().includes(term))
})

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

const goBack = () => {
  router.get(route('categorias.index'))
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
