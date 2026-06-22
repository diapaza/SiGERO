<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Gestión de Usuarios"
        desc="Administre los usuarios del sistema. Cree, edite y elimine usuarios según las necesidades de su organización."
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
              <span>Agregar <span class="hidden md:inline">usuario</span></span>
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
              placeholder="Buscar usuarios..."
              class-name="flex-1"
            />
          </div>
        </div>

        <BaseDataTable
          v-model:global-filter="search"
          :columns="columns"
          :data="filteredUsers"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="isModalOpen"
      :title="editingUser ? 'Editar Usuario' : 'Agregar Usuario'"
      size="md"
      @close="closeModal"
    >
      <template #body>
        <form class="space-y-4" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <BaseFormField
              label="Nombre de usuario"
              label-for="username"
              :required="true"
              :error="form.errors.username"
            >
              <BaseInput
                id="username"
                v-model="form.username"
                type="text"
                placeholder="Ingrese el nombre de usuario"
                :state="form.errors.username ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('username')"
              />
            </BaseFormField>

            <BaseFormField label="DNI" label-for="dni" :required="true" :error="form.errors.dni">
              <BaseInput
                id="dni"
                v-model="form.dni"
                type="text"
                placeholder="Ingrese el DNI"
                :state="form.errors.dni ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('dni')"
              />
            </BaseFormField>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <BaseFormField
              label="Nombres"
              label-for="nombres"
              :required="true"
              :error="form.errors.nombres"
            >
              <BaseInput
                id="nombres"
                v-model="form.nombres"
                type="text"
                placeholder="Ingrese los nombres"
                :state="form.errors.nombres ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('nombres')"
              />
            </BaseFormField>

            <BaseFormField
              label="Apellidos"
              label-for="apellidos"
              :required="true"
              :error="form.errors.apellidos"
            >
              <BaseInput
                id="apellidos"
                v-model="form.apellidos"
                type="text"
                placeholder="Ingrese los apellidos"
                :state="form.errors.apellidos ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('apellidos')"
              />
            </BaseFormField>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <BaseFormField
              label="Número de WhatsApp"
              label-for="whatsapp_number"
              :error="form.errors.whatsapp_number"
            >
              <BaseInput
                id="whatsapp_number"
                v-model="form.whatsapp_number"
                type="text"
                placeholder="Ingrese el número de WhatsApp"
                :state="form.errors.whatsapp_number ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('whatsapp_number')"
              />
            </BaseFormField>

            <BaseFormField label="Rol" label-for="role_id" :error="form.errors.role_id">
              <BaseSelectSearch
                id="role_id"
                v-model="form.role_id"
                :options="roleOptions"
                :searchable="false"
                placeholder="Seleccione un rol"
                class-name="w-full"
              />
            </BaseFormField>
          </div>

          <BaseFormField
            label="Contraseña"
            label-for="password"
            :required="!editingUser"
            :error="form.errors.password"
          >
            <BasePasswordInput
              id="password"
              v-model="form.password"
              :placeholder="
                editingUser
                  ? 'Dejar vacío para mantener la actual'
                  : 'Contraseña generada automáticamente'
              "
              class-name="w-full"
              @blur="validateSingleField('password')"
            />
            <p v-if="!editingUser" class="mt-1 text-sm text-gray-500">
              Se genera automáticamente. Puede editarla si lo desea.
            </p>
          </BaseFormField>

          <BaseFormField
            v-if="editingUser && form.password"
            label="Confirmar contraseña"
            label-for="password_confirmation"
            :error="form.errors.password_confirmation"
          >
            <BasePasswordInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              placeholder="Confirme la nueva contraseña"
              class-name="w-full"
            />
          </BaseFormField>
        </form>
      </template>

      <template #actions>
        <BaseButton variant="outline" :disabled="form.processing" @click="closeModal">
          Cancelar
        </BaseButton>
        <BaseButton variant="primary" :disabled="form.processing" @click="submitForm">
          {{ form.processing ? 'Guardando...' : editingUser ? 'Actualizar' : 'Crear' }}
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
import BaseSelectSearch from '@/components/base/BaseSelectSearch.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'
import { PlusIcon, EditIcon, TrashIcon } from '@/icons'
import { useForm } from '@inertiajs/vue3'
import { useDialog } from '@/composables/useDialog'
import { useValidation } from '@/composables/useValidation'
import { toast } from 'vue-sonner'
import type { User, Role } from '@/types/models'
import { formatDate } from '@/utils/date'

const pageTitle = ref('Usuarios')
const search = ref('')
const isModalOpen = ref(false)
const editingUser = ref<User | null>(null)

const page = usePage()
const { confirm } = useDialog()

const form = useForm({
  username: '',
  dni: '',
  nombres: '',
  apellidos: '',
  whatsapp_number: '',
  role_id: '',
  password: '',
  password_confirmation: '',
})

const { validate, validateSingleField } = useValidation(form, 'user', {
  username: 'nombre de usuario',
  dni: 'DNI',
  nombres: 'nombres',
  apellidos: 'apellidos',
  whatsapp_number: 'número de WhatsApp',
  password: 'contraseña',
})

const pageProps = computed(() => page.props as any)
const users = computed<User[]>(() => pageProps.value.users ?? [])
const roles = computed<Role[]>(() => pageProps.value.roles ?? [])
const trashedCount = computed(() => pageProps.value.trashedCount ?? 0)
const authUserId = computed(() => pageProps.value.auth?.user?.id)

const roleOptions = computed(() =>
  roles.value.map((role) => ({
    value: String(role.id),
    label: role.nombre,
  })),
)

const defaultRoleId = computed(() => {
  const practicante = roles.value.find(
    (r) => r.nombre.toLowerCase().includes('practicante') || r.id === 3,
  )
  return practicante ? String(practicante.id) : roles.value[0] ? String(roles.value[0].id) : ''
})

const filteredUsers = computed(() => {
  if (!search.value) return users.value
  const term = search.value.toLowerCase()
  return users.value.filter(
    (user) =>
      user.username.toLowerCase().includes(term) ||
      user.dni.toLowerCase().includes(term) ||
      user.nombres.toLowerCase().includes(term) ||
      user.apellidos.toLowerCase().includes(term),
  )
})

const generatePassword = (nombres: string, apellidos: string): string => {
  const nombre = nombres.split(' ')[0] || ''
  const apellido = apellidos.split(' ')[0] || ''
  const symbols = ['@', '#', '$', '%', '&', '!']
  const symbol = symbols[Math.floor(Math.random() * symbols.length)]
  const year = new Date().getFullYear()
  return `${nombre}${apellido}${symbol}${year}`
}

const openCreateModal = () => {
  editingUser.value = null
  form.reset()
  form.role_id = defaultRoleId.value
  form.password = generatePassword(form.nombres, form.apellidos)
  isModalOpen.value = true
}

const openEditModal = (user: User) => {
  editingUser.value = user
  form.username = user.username
  form.dni = user.dni
  form.nombres = user.nombres
  form.apellidos = user.apellidos
  form.whatsapp_number = user.whatsapp_number ?? ''
  form.role_id = user.role_id ? String(user.role_id) : ''
  form.password = ''
  form.password_confirmation = ''
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  editingUser.value = null
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (!validate()) return

  if (editingUser.value) {
    const data: Record<string, unknown> = {
      username: form.username,
      dni: form.dni,
      nombres: form.nombres,
      apellidos: form.apellidos,
      whatsapp_number: form.whatsapp_number,
      role_id: form.role_id,
    }

    if (form.password) {
      data.password = form.password
      data.password_confirmation = form.password_confirmation
    }

    form
      .transform(() => data)
      .put(route('users.update', editingUser.value.id), {
        onSuccess: () => {
          closeModal()
        },
      })
  } else {
    form.post(route('users.store'), {
      onSuccess: () => {
        closeModal()
      },
    })
  }
}

const deleteUser = async (user: User) => {
  const confirmed = await confirm({
    title: 'Eliminar usuario',
    description: `¿Estás seguro de eliminar el usuario "${user.username}"? Esta acción no se puede deshacer.`,
    icon: 'warning',
    confirmLabel: 'Eliminar',
    destructive: true,
  })

  if (confirmed) {
    router.delete(route('users.destroy', user.id))
  }
}

const goToTrashed = () => {
  router.get(route('users.trashed'))
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
    accessorKey: 'dni',
    header: 'DNI',
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
    accessorKey: 'whatsapp_number',
    header: 'WhatsApp',
    cell: (info) => info.getValue() ?? '-',
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
    accessorKey: 'created_at',
    header: 'Fecha de creación',
    cell: (info) => formatDate(info.getValue() as string),
  },
  {
    id: 'acciones',
    header: 'Acciones',
    cell: (info) => {
      const user = info.row.original
      const isCurrentUser = user.id === authUserId.value
      const buttons = [
        h(
          BaseButton,
          {
            variant: 'ghost',
            size: 'sm',
            onClick: () => openEditModal(user),
            class: 'text-brand-500 hover:text-yellow-700',
          },
          () => h(EditIcon, { size: 18 }),
        ),
      ]

      if (!isCurrentUser) {
        buttons.push(
          h(
            BaseButton,
            {
              variant: 'ghost',
              size: 'sm',
              onClick: () => deleteUser(user),
              class: 'text-error-500 hover:text-red-700',
            },
            () => h(TrashIcon, { size: 18 }),
          ),
        )
      }

      return h('div', { class: 'flex items-center gap-2' }, buttons)
    },
  },
])

watch([() => form.nombres, () => form.apellidos], () => {
  if (!editingUser.value) {
    form.password = generatePassword(form.nombres, form.apellidos)
  }
})

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
