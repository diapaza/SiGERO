<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Usuarios Eliminados"
        desc="Lista de usuarios que han sido eliminados. Puede restaurarlos para volver a estar activos."
      >
        <template #header>
          <BaseButton variant="outline" size="sm" :start-icon="ChevronLeftIcon" @click="goBack">
            Volver a Usuarios
          </BaseButton>
        </template>

        <div class="mb-4">
          <BaseInput
            v-model="search"
            placeholder="Buscar usuarios eliminados..."
            class-name="max-w-sm"
          />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredUsers"
          :global-filter="search"
          :page-size="10"
        />

        <div
          v-if="filteredUsers.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          No hay usuarios eliminados.
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
import type { User } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Usuarios Eliminados')
const search = ref('')

const page = usePage()
const { confirm } = useDialog()

const pageProps = computed(() => page.props as any)
const users = computed<User[]>(() => pageProps.value.users ?? [])

const filteredUsers = computed(() => {
  if (!search.value) return users.value
  const term = search.value.toLowerCase()
  return users.value.filter(
    (user) =>
      user.username.toLowerCase().includes(term) ||
      user.nombres.toLowerCase().includes(term) ||
      user.apellidos.toLowerCase().includes(term),
  )
})

const restoreUser = async (user: User) => {
  const confirmed = await confirm({
    title: 'Restaurar usuario',
    description: `¿Estás seguro de restaurar el usuario "${user.username}"?`,
    icon: 'question',
    confirmLabel: 'Restaurar',
    destructive: false,
  })

  if (confirmed) {
    router.post(route('users.restore', user.id))
  }
}

const goBack = () => {
  router.get(route('users.index'))
}

const columns = computed<ColumnDef<User>[]>(() => [
  {
    accessorKey: 'id',
    header: 'ID',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'username',
    header: 'Usuario',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'nombres',
    header: 'Nombres',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'apellidos',
    header: 'Apellidos',
    cell: (info) => info.getValue(),
  },
  {
    accessorKey: 'role',
    header: 'Rol',
    cell: (info) => {
      const user = info.row.original
      return user.role?.nombre ?? '-'
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
      const user = info.row.original
      return h(
        BaseButton,
        {
          variant: 'ghost',
          size: 'sm',
          onClick: () => restoreUser(user),
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
