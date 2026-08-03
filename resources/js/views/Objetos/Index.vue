<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Gestión de Objetos"
        desc="Administre los objetos del sistema. Cree, edite y elimine objetos según las necesidades de su organización."
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
              v-if="hasPermission('gestionar objetos')"
              variant="primary"
              size="sm"
              @click="openCreateModal"
            >
              <template #start>
                <PlusIcon :size="18" />
              </template>
              <span>Agregar <span class="hidden md:inline">objeto</span></span>
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
              placeholder="Buscar objetos..."
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

    <!-- Modal Crear / Editar -->
    <BaseModal
      v-model:is-open="modal.isOpen.value"
      :title="editingEntity ? 'Editar Objeto' : 'Agregar Objeto'"
      size="lg"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <template v-if="!editingEntity">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <BaseFormField label="Código" label-for="codigo" :error="form.errors.codigo">
                  <BaseInput
                    id="codigo"
                    v-model="form.codigo"
                    type="text"
                    placeholder="4 o 12 dígitos (vacío = auto-generar)"
                    :state="form.errors.codigo ? 'error' : 'default'"
                    class-name="w-full"
                    @blur="validateSingleField('codigo')"
                  />
                </BaseFormField>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  Deje vacío para auto-generar.
                </p>
              </div>

              <BaseFormField
                label="Nombre"
                label-for="nombre"
                :required="true"
                :error="form.errors.nombre"
              >
                <BaseInput
                  id="nombre"
                  v-model="form.nombre"
                  type="text"
                  placeholder="Ingrese el nombre"
                  :state="form.errors.nombre ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('nombre')"
                />
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField label="Modelo" label-for="modelo" :error="form.errors.modelo">
                <BaseInput
                  id="modelo"
                  v-model="form.modelo"
                  type="text"
                  placeholder="Ingrese el modelo"
                  :state="form.errors.modelo ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('modelo')"
                />
              </BaseFormField>

              <BaseFormField label="Marca" label-for="marca_id" :error="form.errors.marca_id">
                <BaseSelectSearch
                  id="marca_id"
                  v-model="form.marca_id"
                  :options="marcaOptions"
                  creatable
                  create-label='Crear marca "{text}"'
                  placeholder="Buscar o crear marca..."
                  class-name="w-full"
                  @create="handleCreateMarca"
                />
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField
                label="Categoría"
                label-for="categoria_id"
                :error="form.errors.categoria_id"
              >
                <BaseSelectSearch
                  id="categoria_id"
                  v-model="form.categoria_id"
                  :options="categoriaOptions"
                  creatable
                  create-label='Crear categoría "{text}"'
                  placeholder="Buscar o crear categoría..."
                  class-name="w-full"
                  @create="handleCreateCategoria"
                />
              </BaseFormField>

              <BaseFormField label="Serie" label-for="serie" :error="form.errors.serie">
                <BaseInput
                  id="serie"
                  v-model="form.serie"
                  type="text"
                  placeholder="Ingrese el número de serie"
                  :state="form.errors.serie ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('serie')"
                />
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField
                label="Descripción"
                label-for="descripcion"
                :error="form.errors.descripcion"
              >
                <BaseTextarea
                  id="descripcion"
                  v-model="form.descripcion"
                  placeholder="Ingrese la descripción"
                  :rows="5"
                  :state="form.errors.descripcion ? 'error' : 'default'"
                  class-name="w-full"
                />
              </BaseFormField>

              <BaseFormField label="Imagen" label-for="foto">
                <BaseImageDropzone
                  v-model="form.foto"
                  :upload-url="uploadUrl"
                  @uploaded="onImageUploaded"
                  @removed="onImageRemoved"
                />
              </BaseFormField>
            </div>
          </template>

          <template v-else>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField
                label="Nombre"
                label-for="nombre"
                :required="true"
                :error="form.errors.nombre"
              >
                <BaseInput
                  id="nombre"
                  v-model="form.nombre"
                  type="text"
                  placeholder="Ingrese el nombre"
                  :state="form.errors.nombre ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('nombre')"
                />
              </BaseFormField>

              <BaseFormField label="Modelo" label-for="modelo" :error="form.errors.modelo">
                <BaseInput
                  id="modelo"
                  v-model="form.modelo"
                  type="text"
                  placeholder="Ingrese el modelo"
                  :state="form.errors.modelo ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('modelo')"
                />
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField label="Marca" label-for="marca_id" :error="form.errors.marca_id">
                <BaseSelectSearch
                  id="marca_id"
                  v-model="form.marca_id"
                  :options="marcaOptions"
                  creatable
                  create-label='Crear marca "{text}"'
                  placeholder="Buscar o crear marca..."
                  class-name="w-full"
                  @create="handleCreateMarca"
                />
              </BaseFormField>

              <BaseFormField
                label="Categoría"
                label-for="categoria_id"
                :error="form.errors.categoria_id"
              >
                <BaseSelectSearch
                  id="categoria_id"
                  v-model="form.categoria_id"
                  :options="categoriaOptions"
                  creatable
                  create-label='Crear categoría "{text}"'
                  placeholder="Buscar o crear categoría..."
                  class-name="w-full"
                  @create="handleCreateCategoria"
                />
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField label="Serie" label-for="serie" :error="form.errors.serie">
                <BaseInput
                  id="serie"
                  v-model="form.serie"
                  type="text"
                  placeholder="Ingrese el número de serie"
                  :state="form.errors.serie ? 'error' : 'default'"
                  class-name="w-full"
                  @blur="validateSingleField('serie')"
                />
              </BaseFormField>

              <BaseFormField label="Estado" label-for="disponible">
                <BaseBadge
                  :color="editingEntity?.disponible ? 'success' : 'error'"
                  :start-icon="editingEntity?.disponible ? CheckSmallIcon : CloseSmallIcon"
                  size="sm"
                >
                  {{ editingEntity?.disponible ? 'Disponible' : 'Prestado' }}
                </BaseBadge>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  Se actualiza automáticamente con los movimientos.
                </p>
              </BaseFormField>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <BaseFormField
                label="Descripción"
                label-for="descripcion"
                :error="form.errors.descripcion"
              >
                <BaseTextarea
                  id="descripcion"
                  v-model="form.descripcion"
                  placeholder="Ingrese la descripción"
                  :rows="5"
                  :state="form.errors.descripcion ? 'error' : 'default'"
                  class-name="w-full"
                />
              </BaseFormField>

              <BaseFormField label="Imagen" label-for="foto">
                <BaseImageDropzone
                  v-model="form.foto"
                  :upload-url="uploadUrl"
                  @uploaded="onImageUploaded"
                  @removed="onImageRemoved"
                />
              </BaseFormField>
            </div>
          </template>
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

    <!-- Modal Ver Objeto -->
    <BaseModal
      v-model:is-open="isViewModalOpen"
      :title="viewingObjeto?.nombre ?? 'Detalle del Objeto'"
      size="lg"
      @close="closeViewModal"
    >
      <template #body>
        <div v-if="viewingObjeto" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <div class="space-y-3">
            <div>
              <span class="text-sm font-medium text-gray-500">Código:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.codigo }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Nombre:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.nombre }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Modelo:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.modelo ?? 'Sin modelo' }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Marca:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.marca?.nombre ?? 'Sin marca' }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Categoría:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.categoria?.nombre ?? 'Sin categoría' }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Serie:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.serie ?? 'Sin serie' }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Disponible: </span>
              <br />
              <BaseBadge
                :color="viewingObjeto.disponible ? 'success' : 'error'"
                :start-icon="viewingObjeto.disponible ? CheckSmallIcon : CloseSmallIcon"
                size="sm"
              >
                {{ viewingObjeto.disponible ? 'Si' : 'No' }}
              </BaseBadge>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Descripción:</span>
              <p class="text-sm text-gray-900">
                {{ viewingObjeto.descripcion ?? 'Sin descripción' }}
              </p>
            </div>
            <div>
              <span class="text-sm font-medium text-gray-500">Fecha de creación:</span>
              <p class="text-sm text-gray-900">
                {{ formatDate(viewingObjeto.created_at) }}
              </p>
            </div>
          </div>
          <div v-if="viewingObjeto.foto" class="flex items-center justify-center">
            <img
              :src="'/storage/' + viewingObjeto.foto"
              :alt="viewingObjeto.nombre"
              class="max-w-full max-h-80 rounded-lg object-contain border border-gray-200"
            />
          </div>
          <div v-else class="flex items-center justify-center">
            <div class="text-center text-gray-400">
              <p class="text-sm">Sin imagen</p>
            </div>
          </div>
        </div>
      </template>
    </BaseModal>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, h } from 'vue'
import { router } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import axios from 'axios'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import BaseSelectSearch from '@/components/base/BaseSelectSearch.vue'
import BaseTextarea from '@/components/base/BaseTextarea.vue'
import BaseImageDropzone from '@/components/base/BaseImageDropzone.vue'
import BaseBadge from '@/components/base/BaseBadge.vue'
import { PlusIcon, TrashIcon, EyeOffIcon, CloseSmallIcon, CheckSmallIcon, EditIcon } from '@/icons'
import { useCrudIndex } from '@/composables/useCrudIndex'
import { useCrudColumns } from '@/composables/useCrudColumns'
import { useValidation } from '@/composables/useValidation'
import { usePermissions } from '@/composables/usePermissions'
import { toast } from 'vue-sonner'
import type { Objeto, Marca, Categoria } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Objetos')
const isViewModalOpen = ref(false)
const viewingObjeto = ref<Objeto | null>(null)
const uploadedImagePath = ref<string | null>(null)

const { hasPermission } = usePermissions()

const {
  search,
  editingEntity,
  form,
  filteredEntities,
  trashedCount,
  modal,
  pageProps,
  openCreateModal: baseOpenCreateModal,
  openEditModal: baseOpenEditModal,
  closeModal: baseCloseModal,
  submitForm: baseSubmitForm,
  deleteEntity,
  goToTrashed,
} = useCrudIndex<Objeto>({
  entityName: 'objeto',
  entityLabel: 'objeto',
  routePrefix: 'objetos',
  searchFields: ['codigo', 'nombre'],
  createFormFields: {
    codigo: '',
    nombre: '',
    modelo: '',
    descripcion: '',
    marca_id: '',
    categoria_id: '',
    foto: '',
    serie: '',
  },
})

const { idColumn, fieldColumn, dateColumn, customColumn, badgeColumn, addActionsColumn } =
  useCrudColumns<Objeto>()

const { validate, validateSingleField } = useValidation(form, 'objeto', {
  codigo: 'código',
  nombre: 'nombre',
  modelo: 'modelo',
  marca_id: 'marca',
  categoria_id: 'categoría',
  serie: 'serie',
  descripcion: 'descripción',
})

const marcas = computed<Marca[]>(() => pageProps.value.marcas ?? [])
const categorias = computed<Categoria[]>(() => pageProps.value.categorias ?? [])
const uploadUrl = route('objetos.upload-image')

const marcaOptions = computed(() =>
  marcas.value.map((m) => ({
    value: String(m.id),
    label: m.nombre,
  })),
)

const categoriaOptions = computed(() =>
  categorias.value.map((c) => ({
    value: String(c.id),
    label: c.nombre,
  })),
)

const openCreateModal = () => {
  baseOpenCreateModal()
  uploadedImagePath.value = null
}

const openEditModal = (objeto: Objeto) => {
  baseOpenEditModal(objeto)
  uploadedImagePath.value = null
}

const openViewModal = (objeto: Objeto) => {
  viewingObjeto.value = objeto
  isViewModalOpen.value = true
}

const closeModal = () => {
  if (uploadedImagePath.value) {
    axios.post(route('objetos.delete-image'), { path: uploadedImagePath.value })
  }
  baseCloseModal()
  uploadedImagePath.value = null
}

const closeViewModal = () => {
  isViewModalOpen.value = false
  viewingObjeto.value = null
}

const onImageUploaded = (path: string) => {
  uploadedImagePath.value = path
}

const onImageRemoved = async () => {
  if (uploadedImagePath.value) {
    await axios.post(route('objetos.delete-image'), { path: uploadedImagePath.value })
    uploadedImagePath.value = null
  }
}

const handleCreateMarca = async (name: string) => {
  try {
    await axios.post(route('marcas.store'), { nombre: name })
    toast.success(`Marca "${name}" creada correctamente.`)
    router.reload({
      only: ['marcas'],
      onSuccess: () => {
        const newMarca = marcas.value.find((m) => m.nombre.toLowerCase() === name.toLowerCase())
        if (newMarca) {
          form.marca_id = String(newMarca.id)
        }
      },
    })
  } catch {
    toast.error('Error al crear la marca.')
  }
}

const handleCreateCategoria = async (name: string) => {
  try {
    await axios.post(route('categorias.store'), { nombre: name })
    toast.success(`Categoría "${name}" creada correctamente.`)
    router.reload({
      only: ['categorias'],
      onSuccess: () => {
        const newCategoria = categorias.value.find(
          (c) => c.nombre.toLowerCase() === name.toLowerCase(),
        )
        if (newCategoria) {
          form.categoria_id = String(newCategoria.id)
        }
      },
    })
  } catch {
    toast.error('Error al crear la categoría.')
  }
}

const submitForm = () => {
  if (!validate()) return

  if (editingEntity.value) {
    form
      .transform((data) => ({
        ...data,
        foto: data.foto || '',
      }))
      .put(route('objetos.update', editingEntity.value.id), {
        onSuccess: () => {
          uploadedImagePath.value = null
          closeModal()
        },
      })
  } else {
    form
      .transform((data) => ({
        ...data,
        foto: data.foto || '',
      }))
      .post(route('objetos.store'), {
        onSuccess: () => {
          uploadedImagePath.value = null
          closeModal()
        },
      })
  }
}

const deleteObjeto = (objeto: Objeto) => deleteEntity(objeto, objeto.nombre)

const columns = computed<ColumnDef<Objeto>[]>(() => {
  const cols: ColumnDef<Objeto>[] = [
    fieldColumn('codigo', 'Código'),
    fieldColumn('nombre', 'Nombre'),
    fieldColumn('modelo', 'Modelo', 'Sin modelo'),
    fieldColumn('serie', 'Serie', 'Sin serie'),
    customColumn({
      accessorKey: 'marca',
      header: 'Marca',
      cell: (info) => info.row.original.marca?.nombre ?? 'Sin marca',
    }),
    customColumn({
      accessorKey: 'categoria',
      header: 'Categoría',
      cell: (info) => info.row.original.categoria?.nombre ?? 'Sin categoría',
    }),
    badgeColumn(
      'disponible',
      'Disponible',
      { true: 'success', false: 'error' },
      { true: 'Si', false: 'No' },
    ),
    dateColumn('created_at', 'Fecha de creación'),
  ]

  if (hasPermission('gestionar objetos')) {
    cols.push({
      id: 'acciones',
      header: 'Acciones',
      cell: (info) => {
        const objeto = info.row.original
        const buttons: any[] = []

        buttons.push(
          h(
            BaseButton,
            {
              variant: 'ghost',
              size: 'sm',
              onClick: () => openViewModal(objeto),
              class: 'text-blue-500 hover:text-blue-700',
            },
            () => h(EyeOffIcon, { size: 18 }),
          ),
        )

        buttons.push(
          h(
            BaseButton,
            {
              variant: 'ghost',
              size: 'sm',
              onClick: () => openEditModal(objeto),
              class: 'text-brand-500 hover:text-yellow-700',
            },
            () => h(EditIcon, { size: 18 }),
          ),
        )

        buttons.push(
          h(
            BaseButton,
            {
              variant: 'ghost',
              size: 'sm',
              onClick: () => deleteObjeto(objeto),
              class: 'text-error-500 hover:text-red-700',
            },
            () => h(TrashIcon, { size: 18 }),
          ),
        )

        return h('div', { class: 'flex items-center gap-2' }, buttons)
      },
    })
  }

  return cols
})
</script>
