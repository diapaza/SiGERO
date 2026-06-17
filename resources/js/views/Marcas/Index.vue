<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Gestión de Marcas"
        desc="Administre las marcas del sistema. Cree, edite y elimine marcas según las necesidades de su organización."
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
              <span>Agregar <span class="hidden md:inline">marca</span></span>
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
              placeholder="Buscar marcas..."
              class-name="flex-1"
            />
          </div>
        </div>

        <BaseDataTable
          v-model:global-filter="search"
          :columns="columns"
          :data="filteredMarcas"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="isModalOpen"
      :title="editingMarca ? 'Editar Marca' : 'Agregar Marca'"
      size="sm"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <BaseFormField
            label="Nombre de la marca"
            label-for="nombre"
            :required="true"
            :error="form.errors.nombre"
          >
            <BaseInput
              id="nombre"
              v-model="form.nombre"
              type="text"
              placeholder="Ingrese el nombre de la marca"
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
          {{ form.processing ? 'Guardando...' : editingMarca ? 'Actualizar' : 'Crear' }}
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
import type { Marca } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Marcas')
const search = ref('')
const isModalOpen = ref(false)
const editingMarca = ref<Marca | null>(null)

const page = usePage()
const { confirm } = useDialog()

const form = useForm({
  nombre: '',
})

const { validate, validateSingleField } = useValidation(form, 'marca', {
  nombre: 'nombre de la marca',
})

const pageProps = computed(() => page.props as any)
const marcas = computed<Marca[]>(() => pageProps.value.marcas ?? [])
const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)

const filteredMarcas = computed(() => {
  if (!search.value) return marcas.value
  const term = search.value.toLowerCase()
  return marcas.value.filter((marca) => marca.nombre.toLowerCase().includes(term))
})

const openCreateModal = () => {
  editingMarca.value = null
  form.reset()
  isModalOpen.value = true
}

const openEditModal = (marca: Marca) => {
  editingMarca.value = marca
  form.nombre = marca.nombre
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingMarca.value = null
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (!validate()) return
  if (editingMarca.value) {
    form.put(route('marcas.update', editingMarca.value.id), {
      onSuccess: () => {
        closeModal()
      },
    })
  } else {
    form.post(route('marcas.store'), {
      onSuccess: () => {
        closeModal()
      },
    })
  }
}

const deleteMarca = async (marca: Marca) => {
  const confirmed = await confirm({
    title: 'Eliminar marca',
    description: `¿Estás seguro de eliminar la marca "${marca.nombre}"? Esta acción no se puede deshacer.`,
    icon: 'warning',
    confirmLabel: 'Eliminar',
    destructive: true,
  })

  if (confirmed) {
    router.delete(route('marcas.destroy', marca.id))
  }
}

const goToTrashed = () => {
  router.get(route('marcas.trashed'))
}

const columns = computed<ColumnDef<Marca>[]>(() => [
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
      const marca = info.row.original
      return h('div', { class: 'flex items-center gap-2' }, [
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => openEditModal(marca),
            class: 'text-brand-500 hover:text-yellow-700',
          },
          () => h(EditIcon, { size: 18 }),
        ),
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => deleteMarca(marca),
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
