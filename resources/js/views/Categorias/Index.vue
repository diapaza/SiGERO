<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Gestión de Categorías"
        desc="Administre las categorías del sistema. Cree, edite y elimine categorías según las necesidades de su organización."
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
              <span>Ver eliminados </span><span class="hidden md:inline">({{ trashedCount }})</span>
            </BaseButton>
            <BaseButton
              v-if="hasPermission('gestionar categorias')"
              variant="primary"
              size="sm"
              @click="openCreateModal"
            >
              <template #start>
                <PlusIcon :size="18" />
              </template>
              <span>Agregar <span class="hidden md:inline">categoría</span></span>
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
              placeholder="Buscar categorías..."
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
      :title="editingEntity ? 'Editar Categoría' : 'Agregar Categoría'"
      size="sm"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <BaseFormField
            label="Nombre de la categoría"
            label-for="nombre"
            :required="true"
            :error="form.errors.nombre"
          >
            <BaseInput
              id="nombre"
              v-model="form.nombre"
              type="text"
              placeholder="Ingrese el nombre de la categoría"
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
          {{ form.processing ? 'Guardando...' : editingEntity ? 'Actualizar' : 'Crear' }}
        </BaseButton>
      </template>
    </BaseModal>
  </AdminLayout>
</template>

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
import { PlusIcon, TrashIcon } from '@/icons'
import { useCrudIndex } from '@/composables/useCrudIndex'
import { useCrudColumns } from '@/composables/useCrudColumns'
import { useValidation } from '@/composables/useValidation'
import type { Categoria } from '@/types/models'

const pageTitle = ref('Categorías')

const {
  search,
  editingEntity,
  form,
  filteredEntities,
  trashedCount,
  modal,
  hasPermission,
  openCreateModal,
  openEditModal,
  closeModal,
  submitForm: baseSubmitForm,
  deleteEntity,
  goToTrashed,
} = useCrudIndex<Categoria>({
  entityName: 'categoria',
  entityLabel: 'categoría',
  routePrefix: 'categorias',
  searchFields: ['nombre'],
  createFormFields: { nombre: '' },
})

const { idColumn, fieldColumn, dateColumn, addActionsColumn } = useCrudColumns<Categoria>()

const { validate, validateSingleField } = useValidation(form, 'categoria', {
  nombre: 'nombre de la categoría',
})

const submitForm = () => {
  if (!validate()) return
  baseSubmitForm()
}

const columns = computed<ColumnDef<Categoria>[]>(() => {
  const cols: ColumnDef<Categoria>[] = [
    idColumn(),
    fieldColumn('nombre', 'Nombre'),
    dateColumn('created_at', 'Fecha de creación'),
  ]

  return addActionsColumn(cols, {
    permission: 'gestionar categorias',
    edit: { onClick: openEditModal },
    delete: {
      onClick: (categoria: Categoria) => deleteEntity(categoria, categoria.nombre),
      title: 'Eliminar categoría',
      description: '¿Estás seguro de eliminar la categoría',
      displayName: (categoria: Categoria) => categoria.nombre,
    },
  })
})
</script>
