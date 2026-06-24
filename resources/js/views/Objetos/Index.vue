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
            <BaseButton variant="primary" size="sm" @click="openCreateModal">
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
          :data="filteredObjetos"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <!-- Modal Crear / Editar -->
    <BaseModal
      v-model:is-open="isModalOpen"
      :title="editingObjeto ? 'Editar Objeto' : 'Agregar Objeto'"
      size="lg"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
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

            <BaseFormField label="Disponible" label-for="disponible">
              <BaseCheckbox id="disponible" v-model="form.disponible" label="Disponible" />
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
        </form>
      </template>

      <template #actions>
        <BaseButton variant="outline" :disabled="form.processing" @click="closeModal">
          Cancelar
        </BaseButton>
        <BaseButton variant="primary" :disabled="form.processing" @click="submitForm">
          {{ form.processing ? 'Guardando...' : editingObjeto ? 'Actualizar' : 'Crear' }}
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
                :start-icon="viewingObjeto.disponible ? CheckIcon : CloseIcon"
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
import { ref, computed, h, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
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
import BaseCheckbox from '@/components/base/BaseCheckbox.vue'
import BaseTextarea from '@/components/base/BaseTextarea.vue'
import BaseImageDropzone from '@/components/base/BaseImageDropzone.vue'
import BaseBadge from '@/components/base/BaseBadge.vue'
import { PlusIcon, EditIcon, TrashIcon, EyeOffIcon, CheckIcon, CloseIcon } from '@/icons'
import { useForm } from '@inertiajs/vue3'
import { useDialog } from '@/composables/useDialog'
import { useValidation } from '@/composables/useValidation'
import { toast } from 'vue-sonner'
import type { Objeto, Marca, Categoria } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Objetos')
const search = ref('')
const isModalOpen = ref(false)
const isViewModalOpen = ref(false)
const editingObjeto = ref<Objeto | null>(null)
const viewingObjeto = ref<Objeto | null>(null)
const uploadedImagePath = ref<string | null>(null)

const page = usePage()
const { confirm } = useDialog()

const form = useForm({
  nombre: '',
  modelo: '',
  descripcion: '',
  marca_id: '',
  categoria_id: '',
  foto: '',
  serie: '',
  disponible: true,
})

const { validate, validateSingleField } = useValidation(form, 'objeto', {
  nombre: 'nombre',
  modelo: 'modelo',
  marca_id: 'marca',
  categoria_id: 'categoría',
  serie: 'serie',
  descripcion: 'descripción',
})

const pageProps = computed(() => page.props as any)
const objetos = computed<Objeto[]>(() => pageProps.value.objetos ?? [])
const marcas = computed<Marca[]>(() => pageProps.value.marcas ?? [])
const categorias = computed<Categoria[]>(() => pageProps.value.categorias ?? [])
const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)
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

const filteredObjetos = computed(() => {
  if (!search.value) return objetos.value
  const term = search.value.toLowerCase()
  return objetos.value.filter(
    (obj) =>
      obj.codigo.toLowerCase().includes(term) ||
      obj.nombre.toLowerCase().includes(term) ||
      obj.marca?.nombre?.toLowerCase().includes(term) ||
      obj.categoria?.nombre?.toLowerCase().includes(term),
  )
})

const openCreateModal = () => {
  editingObjeto.value = null
  form.reset()
  form.disponible = true
  uploadedImagePath.value = null
  isModalOpen.value = true
}

const openEditModal = (objeto: Objeto) => {
  editingObjeto.value = objeto
  form.nombre = objeto.nombre
  form.modelo = objeto.modelo ?? ''
  form.descripcion = objeto.descripcion ?? ''
  form.marca_id = objeto.marca_id ? String(objeto.marca_id) : ''
  form.categoria_id = objeto.categoria_id ? String(objeto.categoria_id) : ''
  form.foto = objeto.foto ?? ''
  form.serie = objeto.serie ?? ''
  form.disponible = objeto.disponible
  uploadedImagePath.value = null
  isModalOpen.value = true
}

const openViewModal = (objeto: Objeto) => {
  viewingObjeto.value = objeto
  isViewModalOpen.value = true
}

const closeModal = () => {
  if (uploadedImagePath.value) {
    axios.post(route('objetos.delete-image'), { path: uploadedImagePath.value })
  }
  isModalOpen.value = false
  editingObjeto.value = null
  uploadedImagePath.value = null
  form.reset()
  form.clearErrors()
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

  if (editingObjeto.value) {
    form
      .transform((data) => ({
        ...data,
        foto: data.foto || '',
      }))
      .put(route('objetos.update', editingObjeto.value.id), {
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

const deleteObjeto = async (objeto: Objeto) => {
  const confirmed = await confirm({
    title: 'Eliminar objeto',
    description: `¿Estás seguro de eliminar el objeto "${objeto.nombre}"? Esta acción no se puede deshacer.`,
    icon: 'warning',
    confirmLabel: 'Eliminar',
    destructive: true,
  })

  if (confirmed) {
    router.delete(route('objetos.destroy', objeto.id))
  }
}

const goToTrashed = () => {
  router.get(route('objetos.trashed'))
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
    accessorKey: 'modelo',
    header: 'Modelo',
    cell: (info) => info.getValue() ?? 'Sin modelo',
  },
  {
    accessorKey: 'serie',
    header: 'Serie',
    cell: (info) => info.getValue() ?? 'Sin serie',
  },
  {
    accessorKey: 'marca',
    header: 'Marca',
    cell: (info) => {
      const objeto = info.row.original
      return objeto.marca?.nombre ?? 'Sin marca'
    },
  },
  {
    accessorKey: 'categoria',
    header: 'Categoría',
    cell: (info) => {
      const objeto = info.row.original
      return objeto.categoria?.nombre ?? 'Sin categoría'
    },
  },
  {
    accessorKey: 'disponible',
    header: 'Disponible',
    cell: (info) => {
      const disponible = info.getValue() as boolean
      return h(
        BaseBadge,
        {
          color: disponible ? 'success' : 'error',
          startIcon: disponible ? CheckIcon : CloseIcon,
          size: 'sm',
        },
        () => (disponible ? 'Si' : 'No'),
      )
    },
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
      const objeto = info.row.original
      return h('div', { class: 'flex items-center gap-2' }, [
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
