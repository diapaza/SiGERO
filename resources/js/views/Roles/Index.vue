<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Gestión de Roles"
        desc="Administre los roles del sistema. Cree, edite y elimine roles según las necesidades de su organización."
      >
        <template #header>
          <div class="flex flex-wrap items-center gap-3">
            <BaseButton
              v-if="trashedCount > 0"
              variant="outline"
              size="sm"
              :start-icon="TrashIcon"
              @click="goToTrashed"
            >
              <span>Ver eliminados ({{ trashedCount }})</span>
            </BaseButton>
            <BaseButton variant="primary" size="sm" @click="openCreateModal">
              <template #start>
                <PlusIcon :size="18" />
              </template>
              <span>Agregar Rol</span>
            </BaseButton>
          </div>
        </template>

        <div class="mb-4">
          <BaseInput v-model="search" placeholder="Buscar roles..." class-name="max-w-sm" />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredRoles"
          :global-filter="search"
          :page-size="10"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="isModalOpen"
      :title="editingRole ? 'Editar Rol' : 'Agregar Rol'"
      size="sm"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <BaseFormField
            label="Nombre del rol"
            label-for="nombre"
            :required="true"
            :error="form.errors.nombre"
          >
            <BaseInput
              id="nombre"
              v-model="form.nombre"
              type="text"
              placeholder="Ingrese el nombre del rol"
              :state="form.errors.nombre ? 'error' : 'default'"
              class-name="w-full"
              @blur="validateSingleField('nombre')"
            />
          </BaseFormField>
        </form>
      </template>

      <template #actions>
        <BaseButton variant="outline" :disabled="form.processing" @click="closeModal">
          Cancelar
        </BaseButton>
        <BaseButton variant="primary" :disabled="form.processing" @click="submitForm">
          {{ form.processing ? 'Guardando...' : editingRole ? 'Actualizar' : 'Crear' }}
        </BaseButton>
      </template>
    </BaseModal>
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
import BaseModal from '@/components/base/BaseModal.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import { PlusIcon, EditIcon, TrashIcon } from '@/icons'
import { useForm } from '@inertiajs/vue3'
import { useDialog } from '@/composables/useDialog'
import { useValidation } from '@/composables/useValidation'
import { toast } from 'vue-sonner'
import type { Role } from '@/types/models'

const pageTitle = ref('Roles')
const search = ref('')
const isModalOpen = ref(false)
const editingRole = ref<Role | null>(null)

const page = usePage()
const { confirm } = useDialog()

const form = useForm({
  nombre: '',
})

const { validate, validateSingleField } = useValidation(form, 'role', {
  nombre: 'nombre del rol',
})

const pageProps = computed(() => page.props as any)
const roles = computed<Role[]>(() => pageProps.value.roles ?? [])
const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)

const filteredRoles = computed(() => {
  if (!search.value) return roles.value
  const term = search.value.toLowerCase()
  return roles.value.filter((role) => role.nombre.toLowerCase().includes(term))
})

const openCreateModal = () => {
  editingRole.value = null
  form.reset()
  isModalOpen.value = true
}

const openEditModal = (role: Role) => {
  editingRole.value = role
  form.nombre = role.nombre
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingRole.value = null
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (!validate()) return
  if (editingRole.value) {
    form.put(route('roles.update', editingRole.value.id), {
      onSuccess: () => {
        closeModal()
        toast.success('Rol actualizado correctamente.')
      },
    })
  } else {
    form.post(route('roles.store'), {
      onSuccess: () => {
        closeModal()
        toast.success('Rol creado correctamente.')
      },
    })
  }
}

const deleteRole = async (role: Role) => {
  const confirmed = await confirm({
    title: 'Eliminar rol',
    description: `¿Estás seguro de eliminar el rol "${role.nombre}"? Esta acción no se puede deshacer.`,
    icon: 'warning',
    confirmLabel: 'Eliminar',
    destructive: true,
  })

  if (confirmed) {
    router.delete(route('roles.destroy', role.id), {
      onSuccess: () => {
        toast.success('Rol eliminado correctamente.')
      },
      onError: () => {
        toast.error('No se puede eliminar un rol con usuarios asignados.')
      },
    })
  }
}

const goToTrashed = () => {
  router.get(route('roles.trashed'))
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
    id: 'acciones',
    header: 'Acciones',
    cell: (info) => {
      const role = info.row.original
      return h('div', { class: 'flex items-center gap-2' }, [
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => openEditModal(role),
            class: 'text-brand-500 hover:text-gray-700',
          },
          () => h(EditIcon, { size: 16 }),
        ),
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => deleteRole(role),
            class: 'text-error-500 hover:text-gray-700',
          },
          () => h(TrashIcon, { size: 16 }),
        ),
      ])
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
