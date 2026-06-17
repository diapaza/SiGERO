<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Roles Eliminados"
        desc="Lista de roles que han sido eliminados. Puede restaurarlos para volver a estar activos."
      >
        <template #header>
          <BaseButton variant="outline" size="sm" :start-icon="ChevronLeftIcon" @click="goBack">
            Volver a Roles
          </BaseButton>
        </template>

        <div class="mb-4">
          <BaseInput
            v-model="search"
            placeholder="Buscar roles eliminados..."
            class-name="max-w-sm"
          />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredRoles"
          :global-filter="search"
          :page-size="10"
        />

        <div
          v-if="filteredRoles.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          No hay roles eliminados.
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
import type { Role } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Roles Eliminados')
const search = ref('')

const page = usePage()
const { confirm } = useDialog()

const pageProps = computed(() => page.props as any)
const roles = computed<Role[]>(() => pageProps.value.roles ?? [])

const filteredRoles = computed(() => {
  if (!search.value) return roles.value
  const term = search.value.toLowerCase()
  return roles.value.filter((role) => role.nombre.toLowerCase().includes(term))
})

const restoreRole = async (role: Role) => {
  const confirmed = await confirm({
    title: 'Restaurar rol',
    description: `¿Estás seguro de restaurar el rol "${role.nombre}"?`,
    icon: 'question',
    confirmLabel: 'Restaurar',
    destructive: false,
  })

  if (confirmed) {
    router.post(
      route('roles.restore', role.id),
      {},
      {
        onSuccess: () => {
          toast.success('Rol restaurado correctamente.')
        },
      },
    )
  }
}

const goBack = () => {
  router.get(route('roles.index'))
}

const columns = computed<ColumnDef<Role>[]>(() => [
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
      const role = info.row.original
      return h(
        BaseButton,
        {
          variant: 'ghost',
          size: 'sm',
          onClick: () => restoreRole(role),
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
