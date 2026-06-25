<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <!-- Card 1: Registrar Movimiento -->
      <ComponentCard
        title="Registrar Movimiento"
        desc="Registre una salida o retorno de un objeto."
      >
        <form class="space-y-4" @submit.prevent="submitForm">
          <!-- Código del Objeto -->
          <BaseFormField
            label="Código del Objeto"
            label-for="codigo"
            :required="true"
            :error="form.errors.objeto_id"
          >
            <div class="flex gap-2">
              <BaseInput
                id="codigo"
                v-model="codigoInput"
                type="text"
                placeholder="Ingrese código (4 o 12 dígitos)"
                :state="form.errors.objeto_id ? 'error' : 'default'"
                class-name="flex-1"
                @keydown.enter.prevent="searchObjeto"
              />
            </div>
          </BaseFormField>

          <!-- Info del Objeto encontrado -->
          <div
            v-if="foundObjeto"
            :class="[
              'rounded-lg border p-3',
              foundObjeto.disponible
                ? 'border-success-300 bg-success-50'
                : 'border-warning-300 bg-warning-50',
            ]"
          >
            <div class="flex items-start gap-3">
              <div
                :class="[
                  'flex h-8 w-8 items-center justify-center rounded-full',
                  foundObjeto.disponible ? 'bg-success-100' : 'bg-warning-100',
                ]"
              >
                <CheckIcon v-if="foundObjeto.disponible" :size="16" class="text-success-600" />
                <WarningIcon v-else :size="16" class="text-warning-600" />
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">
                  {{ foundObjeto.nombre }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ foundObjeto.codigo }} &middot; {{ foundObjeto.marca ?? 'Sin marca' }} &middot;
                  {{ foundObjeto.categoria ?? 'Sin categoría' }}
                </p>
                <p v-if="foundObjeto.modelo" class="text-xs text-gray-500">
                  Modelo: {{ foundObjeto.modelo }}
                </p>
                <p v-if="foundObjeto.serie" class="text-xs text-gray-500">
                  Serie: {{ foundObjeto.serie }}
                </p>
              </div>
              <BaseBadge :color="foundObjeto.disponible ? 'success' : 'warning'" size="sm">
                {{ foundObjeto.disponible ? 'Disponible' : 'Prestado' }}
              </BaseBadge>
            </div>
          </div>

          <div v-if="codigoError" class="rounded-lg border border-error-300 bg-error-50 p-3">
            <p class="text-sm text-error-600">
              {{ codigoError }}
            </p>
          </div>

          <!-- DNI del Responsable (condicional) -->
          <template v-if="foundObjeto && foundObjeto.disponible">
            <BaseFormField
              label="DNI del Responsable"
              label-for="dni"
              :required="true"
              :error="form.errors.user_id"
            >
              <BaseInput
                id="dni"
                ref="dniInputRef"
                v-model="dniInput"
                type="text"
                placeholder="Ingrese DNI (8 dígitos)"
                :state="form.errors.user_id ? 'error' : 'default'"
                class-name="flex-1"
                @keydown.enter.prevent="searchUser"
              />
            </BaseFormField>

            <!-- Info de la Persona encontrada -->
            <div v-if="foundUser" class="rounded-lg border border-success-300 bg-success-50 p-3">
              <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-100">
                  <CheckIcon :size="16" class="text-success-600" />
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">
                    {{ foundUser.name }}
                  </p>
                  <p class="text-xs text-gray-500">DNI: {{ foundUser.dni }}</p>
                  <p v-if="foundUser.whatsapp_number" class="text-xs text-gray-500">
                    Tel: {{ foundUser.whatsapp_number }}
                  </p>
                </div>
              </div>
            </div>

            <div v-if="dniError" class="rounded-lg border border-error-300 bg-error-50 p-3">
              <p class="text-sm text-error-600">
                {{ dniError }}
              </p>
            </div>
          </template>

          <!-- Tipo de Movimiento (auto-determinado, solo lectura) -->
          <div v-if="foundObjeto" class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Tipo:</span>
            <BaseBadge :color="foundObjeto.disponible ? 'info' : 'success'" size="md">
              {{ foundObjeto.disponible ? 'Salida' : 'Retorno' }}
            </BaseBadge>
          </div>

          <!-- Registrado por (solo lectura) -->
          <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Registrado por:</span>
            <span class="text-sm text-gray-900">{{ authUser?.name ?? '—' }}</span>
          </div>

          <!-- Submit -->
          <div class="flex justify-end pt-2">
            <BaseButton
              type="submit"
              variant="primary"
              :disabled="form.processing || !canSubmit"
              :start-icon="SendIcon"
            >
              {{ form.processing ? 'Registrando...' : 'Registrar Movimiento' }}
            </BaseButton>
          </div>
        </form>
      </ComponentCard>

      <!-- Card 2: Lista de Movimientos -->
      <ComponentCard
        title="Movimientos Registrados"
        desc="Historial de salidas y retornos de objetos."
      >
        <div class="mb-4">
          <div class="flex items-center gap-2 max-w-sm">
            <label
              for="search-movimientos"
              class="text-base font-regular text-gray-700 whitespace-nowrap"
            >
              Buscar:
            </label>
            <BaseInput
              id="search-movimientos"
              v-model="search"
              placeholder="Buscar movimientos..."
              class-name="flex-1"
            />
          </div>
        </div>

        <BaseDataTable
          v-model:global-filter="search"
          :columns="columns"
          :data="filteredMovimientos"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <!-- Modal Editar Movimiento -->
    <BaseModal
      v-model:is-open="isEditModalOpen"
      :title="'Editar Movimiento #' + (editingMovimiento?.id ?? '')"
      size="lg"
      @close="closeEditModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitEditForm">
          <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 mt-4">
            <p class="text-sm text-gray-500">
              <span class="font-medium">Objeto:</span>
              {{ editingMovimiento?.objeto?.codigo }} - {{ editingMovimiento?.objeto?.nombre }}
            </p>
            <p class="text-sm text-gray-500">
              <span class="font-medium">Registrado por:</span>
              {{ editingMovimiento?.registrado_por?.name }}
            </p>
          </div>

          <BaseFormField
            label="Tipo de Movimiento"
            label-for="edit-tipo"
            :required="true"
            :error="editForm.errors.tipo_movimiento"
          >
            <BaseSelectSearch
              id="edit-tipo"
              v-model="editForm.tipo_movimiento"
              :options="tipoOptions"
              placeholder="Seleccionar tipo..."
              class-name="w-full"
            />
          </BaseFormField>

          <BaseFormField
            label="Quien lo tiene"
            label-for="edit-user"
            :required="true"
            :error="editForm.errors.user_id"
          >
            <BaseSelectSearch
              id="edit-user"
              v-model="editForm.user_id"
              :options="userOptions"
              placeholder="Buscar persona..."
              class-name="w-full"
            />
          </BaseFormField>

          <BaseFormField
            label="Fecha y Hora"
            label-for="edit-fecha"
            :required="true"
            :error="editForm.errors.fecha_hora"
          >
            <BaseInput
              id="edit-fecha"
              v-model="editForm.fecha_hora"
              type="datetime-local"
              :state="editForm.errors.fecha_hora ? 'error' : 'default'"
              class-name="w-full"
            />
          </BaseFormField>
        </form>
      </template>

      <template #actions>
        <BaseButton variant="outline" :disabled="editForm.processing" @click="closeEditModal">
          Cancelar
        </BaseButton>
        <BaseButton variant="primary" :disabled="editForm.processing" @click="submitEditForm">
          {{ editForm.processing ? 'Guardando...' : 'Actualizar' }}
        </BaseButton>
      </template>
    </BaseModal>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, h, watch } from 'vue'
import { usePage, router, useForm } from '@inertiajs/vue3'
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
import BaseBadge from '@/components/base/BaseBadge.vue'
import { EditIcon, SendIcon, CheckIcon, WarningIcon } from '@/icons'
import { useValidation } from '@/composables/useValidation'
import { toast } from 'vue-sonner'
import type { Movimiento, User } from '@/types/models'
import { formatDate, formatDateTime } from '@/utils/date'

const pageTitle = ref('Movimientos')
const search = ref('')

// Auth user
const page = usePage()
const authUser = computed(() => (page.props.auth as any)?.user ?? null)

// Data from controller
const pageProps = computed(() => page.props as any)
const movimientos = computed<Movimiento[]>(() => pageProps.value.movimientos ?? [])
const users = computed<User[]>(() => pageProps.value.users ?? [])

// Registration form state
const codigoInput = ref('')
const dniInput = ref('')
const foundObjeto = ref<any>(null)
const foundUser = ref<any>(null)
const codigoError = ref('')
const dniError = ref('')
const dniInputRef = ref<any>(null)

const form = useForm({
  objeto_id: '',
  user_id: '',
  registrado_por: '',
  tipo_movimiento: '',
  fecha_hora: '',
})

const { validate } = useValidation(form, 'movimiento')

// Edit modal state
const isEditModalOpen = ref(false)
const editingMovimiento = ref<Movimiento | null>(null)

const editForm = useForm({
  tipo_movimiento: '',
  user_id: '',
  fecha_hora: '',
})

const tipoOptions = computed(() => [
  { value: 'salida', label: 'Salida' },
  { value: 'retorno', label: 'Retorno' },
])

const userOptions = computed(() =>
  users.value.map((u) => ({
    value: String(u.id),
    label: `${u.name} (${u.dni})`,
  })),
)

// Auto-tab: when objeto is found, focus next field
const canSubmit = computed(() => {
  if (!foundObjeto.value) return false
  if (foundObjeto.value.disponible && !foundUser.value) return false
  return true
})

// Search object by code
const searchObjeto = async () => {
  const code = codigoInput.value.trim()
  if (!code) return

  // Validate: only 4 or 12 digits
  if (!/^\d{4}$/.test(code) && !/^\d{12}$/.test(code)) {
    codigoError.value = 'El código debe tener exactamente 4 o 12 dígitos.'
    foundObjeto.value = null
    form.objeto_id = ''
    return
  }

  try {
    const response = await axios.get(route('api.objetos.search', code))
    foundObjeto.value = response.data
    form.objeto_id = String(response.data.id)
    codigoError.value = ''

    // Auto-determine tipo_movimiento
    form.tipo_movimiento = response.data.disponible ? 'salida' : 'retorno'

    // If object is not available (retorno), auto-set user_id from last movement
    if (!response.data.disponible) {
      // Find the last movement for this object to get the user
      const lastMovement = movimientos.value.find(
        (m) => m.objeto_id === response.data.id && m.tipo_movimiento === 'salida',
      )
      if (lastMovement) {
        form.user_id = String(lastMovement.user_id)
        foundUser.value = lastMovement.user ?? null
      }
      setTimeout(() => {
        const submitBtn = document.querySelector('button[type="submit"]') as HTMLButtonElement
        submitBtn?.focus()
      }, 100)
    } else {
      // Object is available - need DNI, focus DNI input
      form.user_id = ''
      foundUser.value = null
      dniInput.value = ''
      setTimeout(() => {
        dniInputRef.value?.$el?.querySelector('input')?.focus()
      }, 100)
    }
  } catch (error: any) {
    foundObjeto.value = null
    form.objeto_id = ''
    form.tipo_movimiento = ''
    form.user_id = ''
    foundUser.value = null
    if (error.response?.status === 404) {
      codigoError.value = 'Objeto no encontrado. Verifique el código.'
    } else {
      codigoError.value = 'Error al buscar el objeto.'
    }
  }
}

// Search user by DNI
const searchUser = async () => {
  const dni = dniInput.value.trim()
  if (!dni) return

  // Validate: only 8 digits
  if (!/^\d{8}$/.test(dni)) {
    dniError.value = 'El DNI debe tener exactamente 8 dígitos.'
    foundUser.value = null
    form.user_id = ''
    return
  }

  try {
    const response = await axios.get(route('api.users.search', dni))
    foundUser.value = response.data
    form.user_id = String(response.data.id)
    dniError.value = ''
    setTimeout(() => {
      const submitBtn = document.querySelector('button[type="submit"]') as HTMLButtonElement
      submitBtn?.focus()
    }, 100)
  } catch (error: any) {
    foundUser.value = null
    form.user_id = ''
    if (error.response?.status === 404) {
      dniError.value = 'Persona no encontrada. Verifique el DNI.'
    } else {
      dniError.value = 'Error al buscar la persona.'
    }
  }
}

// Submit registration form
const submitForm = () => {
  if (!canSubmit.value) return

  form.registrado_por = authUser.value?.id ?? ''
  form.fecha_hora = new Date().toISOString().slice(0, 19).replace('T', ' ')

  if (!validate()) return

  form.post(route('movimientos.store'), {
    onSuccess: () => {
      resetForm()
    },
  })
}

// Reset form
const resetForm = () => {
  codigoInput.value = ''
  dniInput.value = ''
  foundObjeto.value = null
  foundUser.value = null
  codigoError.value = ''
  dniError.value = ''
  form.reset()
  form.clearErrors()
}

// Edit modal handlers
const openEditModal = (movimiento: Movimiento) => {
  editingMovimiento.value = movimiento
  editForm.tipo_movimiento = movimiento.tipo_movimiento
  editForm.user_id = String(movimiento.user_id)
  // Format fecha_hora for datetime-local input
  const fecha = new Date(movimiento.fecha_hora)
  const year = fecha.getFullYear()
  const month = String(fecha.getMonth() + 1).padStart(2, '0')
  const day = String(fecha.getDate()).padStart(2, '0')
  const hours = String(fecha.getHours()).padStart(2, '0')
  const minutes = String(fecha.getMinutes()).padStart(2, '0')
  editForm.fecha_hora = `${year}-${month}-${day}T${hours}:${minutes}`
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  editingMovimiento.value = null
  editForm.reset()
  editForm.clearErrors()
}

const submitEditForm = () => {
  if (!editingMovimiento.value) return

  editForm.transform((data) => ({
    ...data,
    user_id: Number(data.user_id),
    fecha_hora: data.fecha_hora ? new Date(data.fecha_hora).toISOString() : '',
  }))

  editForm.put(route('movimientos.update', editingMovimiento.value.id), {
    onSuccess: () => {
      closeEditModal()
    },
  })
}

// Filtered movimientos
const filteredMovimientos = computed(() => {
  if (!search.value) return movimientos.value
  const term = search.value.toLowerCase()
  return movimientos.value.filter(
    (m) =>
      m.id.toString().includes(term) ||
      m.objeto?.codigo?.toLowerCase().includes(term) ||
      m.objeto?.nombre?.toLowerCase().includes(term) ||
      m.user?.name?.toLowerCase().includes(term) ||
      m.registrado_por?.name?.toLowerCase().includes(term) ||
      m.tipo_movimiento?.toLowerCase().includes(term),
  )
})

// Table columns
const columns = computed<ColumnDef<Movimiento>[]>(() => [
  {
    accessorKey: 'id',
    header: 'ID',
    cell: (info) => `${info.getValue()}`,
  },
  {
    accessorKey: 'user',
    header: 'Quien lo tiene',
    cell: (info) => {
      const movimiento = info.row.original
      return movimiento.user?.name ?? '—'
    },
  },
  {
    accessorKey: 'telefono',
    header: 'Teléfono',
    cell: (info) => {
      const movimiento = info.row.original
      return movimiento.user?.whatsapp_number ?? 'No registrado'
    },
  },
  {
    accessorKey: 'objeto',
    header: 'Código',
    cell: (info) => {
      const movimiento = info.row.original
      return movimiento.objeto?.codigo ?? '—'
    },
  },
  {
    accessorKey: 'objetoNombre',
    header: 'Objeto',
    cell: (info) => {
      const movimiento = info.row.original
      return movimiento.objeto?.nombre ?? '—'
    },
  },
  {
    accessorKey: 'registrado_por',
    header: 'Registrado por',
    cell: (info) => {
      const movimiento = info.row.original
      return movimiento.registrado_por?.name ?? '—'
    },
  },
  {
    accessorKey: 'fecha_hora',
    header: 'Fecha',
    cell: (info) => formatDateTime(info.getValue() as string),
  },
  {
    accessorKey: 'tipo_movimiento',
    header: 'Tipo',
    cell: (info) => {
      const tipo = info.getValue() as string
      return h(
        BaseBadge,
        {
          color: tipo === 'salida' ? 'warning' : 'success',
          size: 'sm',
        },
        () => (tipo === 'salida' ? 'Salida' : 'Retorno'),
      )
    },
  },
  {
    id: 'acciones',
    header: 'Acciones',
    cell: (info) => {
      const movimiento = info.row.original
      return h('div', { class: 'flex items-center gap-2' }, [
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => openEditModal(movimiento),
            class: 'text-brand-500 hover:text-yellow-700',
          },
          () => h(EditIcon, { size: 18 }),
        ),
      ])
    },
  },
])

// Flash messages
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

// Auto-search: trigger search when input reaches expected length
watch(codigoInput, (value) => {
  if (value.length === 4 || value.length === 12) {
    searchObjeto()
  }
})

watch(dniInput, (value) => {
  if (value.length === 8) {
    searchUser()
  }
})
</script>
