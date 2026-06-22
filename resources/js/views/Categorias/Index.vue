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
            <BaseButton variant="primary" size="sm" @click="openCreateModal">
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
          :data="filteredCategorias"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="isModalOpen"
      :title="editingCategoria ? 'Editar Categoría' : 'Agregar Categoría'"
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
          {{ form.processing ? 'Guardando...' : editingCategoria ? 'Actualizar' : 'Crear' }}
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
import type { Categoria } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Categorías')
const search = ref('')
const isModalOpen = ref(false)
const editingCategoria = ref<Categoria | null>(null)

const page = usePage()
const { confirm } = useDialog()

const form = useForm({
  nombre: '',
})

const { validate, validateSingleField } = useValidation(form, 'categoria', {
  nombre: 'nombre de la categoría',
})

const pageProps = computed(() => page.props as any)
const categorias = computed<Categoria[]>(() => pageProps.value.categorias ?? [])
const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)

const filteredCategorias = computed(() => {
  if (!search.value) return categorias.value
  const term = search.value.toLowerCase()
  return categorias.value.filter((categoria) => categoria.nombre.toLowerCase().includes(term))
})

const openCreateModal = () => {
  editingCategoria.value = null
  form.reset()
  isModalOpen.value = true
}

const openEditModal = (categoria: Categoria) => {
  editingCategoria.value = categoria
  form.nombre = categoria.nombre
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingCategoria.value = null
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (!validate()) return
  if (editingCategoria.value) {
    form.put(route('categorias.update', editingCategoria.value.id), {
      onSuccess: () => {
        closeModal()
      },
    })
  } else {
    form.post(route('categorias.store'), {
      onSuccess: () => {
        closeModal()
      },
    })
  }
}

const deleteCategoria = async (categoria: Categoria) => {
  const confirmed = await confirm({
    title: 'Eliminar categoría',
    description: `¿Estás seguro de eliminar la categoría "${categoria.nombre}"? Esta acción no se puede deshacer.`,
    icon: 'warning',
    confirmLabel: 'Eliminar',
    destructive: true,
  })

  if (confirmed) {
    router.delete(route('categorias.destroy', categoria.id))
  }
}

const goToTrashed = () => {
  router.get(route('categorias.trashed'))
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
    accessorKey: 'created_at',
    header: 'Fecha de creación',
    cell: (info) => formatDate(info.getValue() as string),
  },
  {
    id: 'acciones',
    header: 'Acciones',
    cell: (info) => {
      const categoria = info.row.original
      return h('div', { class: 'flex items-center gap-2' }, [
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => openEditModal(categoria),
            class: 'text-brand-500 hover:text-yellow-700',
          },
          () => h(EditIcon, { size: 18 }),
        ),
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => deleteCategoria(categoria),
            class: 'text-error-500 hover:text-red-700',
          },
          () => h(TrashIcon, { size: 18 }),
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
