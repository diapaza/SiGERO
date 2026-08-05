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
              v-if="hasPermission('gestionar roles')"
              variant="primary"
              size="sm"
              @click="openCreateModal"
            >
              <template #start>
                <PlusIcon :size="18" />
              </template>
              <span>Agregar <span class="hidden md:inline">rol</span></span>
            </BaseButton>
          </div>
        </template>

        <div class="mb-4">
          <div class="flex items-center gap-2 max-w-sm">
            <label for="search" class="text-base font-regular text-gray-700 whitespace-nowrap"
              >Buscar:</label
            >
            <BaseInput
              id="search"
              v-model="search"
              placeholder="Buscar roles..."
              class-name="flex-1"
            />
          </div>
        </div>

        <BaseDataTable
          v-model:global-filter="search"
          :columns="columns"
          :data="filteredEntities"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="modal.isOpen.value"
      :title="editingEntity ? 'Editar Rol' : 'Agregar Rol'"
      size="sm"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <BaseFormField
            label="Nombre del rol"
            label-for="name"
            :required="true"
            :error="form.errors.name"
          >
            <BaseInput
              id="name"
              v-model="form.name"
              type="text"
              :maxlength="255"
              placeholder="Ingrese el nombre del rol"
              :state="form.errors.name ? 'error' : 'default'"
              class-name="w-full"
              @blur="validateSingleField('name')"
            />
          </BaseFormField>
        </form>
      </template>

      <template #actions>
        <BaseButton variant="outline" :disabled="form.processing" @click="closeModal">
          Cancelar
        </BaseButton>
        <BaseButton variant="primary" :disabled="form.processing" @click="submitForm">
          {{ form.processing ? 'Guardando...' : editingEntity ? 'Actualizar' : 'Crear' }}
        </BaseButton>
      </template>
    </BaseModal>
  </AdminLayout>
</template>

/** * Página de gestión de Roles. * * Vista renderizada por `RoleController@index`. Lista, crea,
edita y elimina * roles. Muestra el conteo de usuarios por rol (`users_count`). Los roles no * se
eliminan de forma suave. * * Props Inertia: `roles`, `trashedCount`, `flash`. */
<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import { PlusIcon } from '@/icons'
import { useCrudIndex } from '@/composables/useCrudIndex'
import { useCrudColumns } from '@/composables/useCrudColumns'
import { useValidation } from '@/composables/useValidation'
import type { Role } from '@/types/models'

const pageTitle = ref('Roles')

const {
  search,
  editingEntity,
  form,
  filteredEntities,
  modal,
  hasPermission,
  openCreateModal,
  openEditModal,
  closeModal,
  submitForm: baseSubmitForm,
  deleteEntity,
} = useCrudIndex<Role>({
  entityName: 'role',
  entityLabel: 'rol',
  routePrefix: 'roles',
  searchFields: ['name'],
  createFormFields: { name: '' },
})

const { idColumn, fieldColumn, dateColumn, addActionsColumn } = useCrudColumns<Role>()

const { validate, validateSingleField } = useValidation(form, 'role', {
  name: 'nombre del rol',
})

const submitForm = () => {
  if (!validate()) return
  baseSubmitForm()
}

const columns = computed<ColumnDef<Role>[]>(() => {
  const cols: ColumnDef<Role>[] = [
    idColumn(),
    fieldColumn('name', 'Nombre'),
    fieldColumn('users_count', 'Usuarios', '0'),
    dateColumn('created_at', 'Fecha de creación'),
  ]

  return addActionsColumn(cols, {
    permission: 'gestionar roles',
    edit: { onClick: openEditModal },
    delete: {
      onClick: (role: Role) => deleteEntity(role, role.name),
      title: 'Eliminar rol',
      description: '¿Estás seguro de eliminar el rol',
      displayName: (role: Role) => role.name,
    },
  })
})
</script>
