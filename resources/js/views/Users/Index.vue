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
            <BaseButton
              v-if="hasPermission('crear usuarios')"
              variant="primary"
              size="sm"
              @click="openCreateModal"
            >
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
          :data="filteredEntities"
          :page-size="5"
        />
      </ComponentCard>
    </div>

    <BaseModal
      v-model:is-open="modal.isOpen.value"
      :title="editingEntity ? 'Editar Usuario' : 'Agregar Usuario'"
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
                :maxlength="255"
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
                :maxlength="8"
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
                :maxlength="120"
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
                :maxlength="120"
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
              :required="true"
              :error="form.errors.whatsapp_number"
            >
              <BaseInput
                id="whatsapp_number"
                v-model="form.whatsapp_number"
                type="text"
                :maxlength="9"
                placeholder="Ingrese el número de WhatsApp"
                :state="form.errors.whatsapp_number ? 'error' : 'default'"
                class-name="w-full"
                @blur="validateSingleField('whatsapp_number')"
              />
            </BaseFormField>

            <BaseFormField label="Rol" label-for="roles" :error="form.errors.roles">
              <BaseSelectSearch
                id="roles"
                v-model="form.roles[0]"
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
            :required="!editingEntity"
            :error="form.errors.password"
          >
            <BasePasswordInput
              id="password"
              v-model="form.password"
              :placeholder="
                editingEntity
                  ? 'Dejar vacío para mantener la actual'
                  : 'Contraseña generada automáticamente'
              "
              class-name="w-full"
              @blur="validateSingleField('password')"
            />
            <p v-if="!editingEntity" class="mt-1 text-sm text-gray-500">
              Se genera automáticamente. Puede editarla si lo desea.
            </p>
          </BaseFormField>

          <BaseFormField
            v-if="editingEntity && form.password"
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
          {{ form.processing ? 'Guardando...' : editingEntity ? 'Actualizar' : 'Crear' }}
        </BaseButton>
      </template>
    </BaseModal>

    <PermissionsModal
      :is-open="isPermissionsModalOpen"
      :user="selectedUserForPermissions"
      :all-permissions="allPermissions"
      :user-permissions="selectedUserPermissions"
      :role-permissions="selectedUserRolePermissions"
      @close="closePermissionsModal"
    />
  </AdminLayout>
</template>

/** * Página de gestión de Usuarios. * * Vista renderizada por `UserController@index`. Lista, crea,
edita y elimina * usuarios, asigna roles y gestiona permisos directos mediante * `PermissionsModal`
(los permisos derivados de rol se muestran bloqueados). * * Props Inertia: `users` (con
`all_permissions` y `role_permissions`), * `roles`, `trashedCount`, `allPermissions`, `flash`. */
<script setup lang="ts">
import { ref, computed, h, watch } from 'vue'
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
import PermissionsModal from '@/components/PermissionsModal.vue'
import { PlusIcon, TrashIcon, LockIcon, EditIcon } from '@/icons'
import { useCrudIndex } from '@/composables/useCrudIndex'
import { useCrudColumns } from '@/composables/useCrudColumns'
import { useValidation } from '@/composables/useValidation'
import type { User, Role, Permission } from '@/types/models'

const pageTitle = ref('Usuarios')

const {
  search,
  editingEntity,
  form,
  filteredEntities,
  trashedCount,
  modal,
  hasPermission,
  pageProps,
  openCreateModal: baseOpenCreateModal,
  openEditModal: baseOpenEditModal,
  closeModal: baseCloseModal,
  deleteEntity,
  goToTrashed,
} = useCrudIndex<User>({
  entityName: 'user',
  entityLabel: 'usuario',
  routePrefix: 'users',
  searchFields: ['username', 'dni', 'nombres', 'apellidos'],
  createFormFields: {
    username: '',
    dni: '',
    nombres: '',
    apellidos: '',
    whatsapp_number: '',
    roles: [''],
    password: '',
    password_confirmation: '',
  },
})

const { idColumn, fieldColumn, dateColumn, customColumn } = useCrudColumns<User>()

const validationLabels = {
  username: 'nombre de usuario',
  dni: 'DNI',
  nombres: 'nombres',
  apellidos: 'apellidos',
  whatsapp_number: 'número de WhatsApp',
  password: 'contraseña',
}

const { validate: validateCreate, validateSingleField: validateSingleFieldCreate } = useValidation(
  form,
  'user',
  validationLabels,
)
const { validate: validateUpdate, validateSingleField: validateSingleFieldUpdate } = useValidation(
  form,
  'userUpdate',
  validationLabels,
)

const validate = () => (editingEntity.value ? validateUpdate() : validateCreate())
const validateSingleField = (field: string) =>
  editingEntity.value ? validateSingleFieldUpdate(field) : validateSingleFieldCreate(field)

const roles = computed<Role[]>(() => pageProps.value.roles ?? [])
const authUserId = computed(() => pageProps.value.auth?.user?.id)
const allPermissions = computed<Permission[]>(() => pageProps.value.allPermissions ?? [])

const roleOptions = computed(() =>
  roles.value.map((role) => ({
    value: role.name,
    label: role.name,
  })),
)

const defaultRoleName = computed(() => {
  const practicante = roles.value.find((r) => r.name.toLowerCase().includes('practicante'))
  return practicante ? practicante.name : (roles.value[0]?.name ?? '')
})

const getUserRoles = (user: User): string => {
  if (!user.roles || user.roles.length === 0) return '-'
  return user.roles.map((r) => r.name).join(', ')
}

const generatePassword = (nombres: string, apellidos: string): string => {
  const nombre = nombres.split(' ')[0] || ''
  const apellido = apellidos.split(' ')[0] || ''
  const symbols = ['@', '#', '$', '%', '&', '!']
  const symbol = symbols[Math.floor(Math.random() * symbols.length)]
  const year = new Date().getFullYear()
  return `${nombre}${apellido}${symbol}${year}`
}

const openCreateModal = () => {
  baseOpenCreateModal()
  form.roles = [defaultRoleName.value]
  form.password = generatePassword(form.nombres, form.apellidos)
  form.password_confirmation = form.password
}

const openEditModal = (user: User) => {
  baseOpenEditModal(user)
  form.roles = user.roles.length > 0 ? [user.roles[0].name] : ['']
  form.password = ''
  form.password_confirmation = ''
}

const closeModal = () => {
  baseCloseModal()
}

const submitForm = () => {
  if (!validate()) return

  const data: Record<string, unknown> = {
    username: form.username,
    dni: form.dni,
    nombres: form.nombres,
    apellidos: form.apellidos,
    whatsapp_number: form.whatsapp_number,
    roles: form.roles.filter((r: string) => r !== ''),
  }

  if (form.password) {
    data.password = form.password
    data.password_confirmation = form.password_confirmation
  }

  if (editingEntity.value) {
    form
      .transform(() => data)
      .put(route('users.update', editingEntity.value!.id), {
        onSuccess: () => closeModal(),
      })
  } else {
    form
      .transform(() => data)
      .post(route('users.store'), {
        onSuccess: () => closeModal(),
      })
  }
}

const deleteUser = (user: User) => deleteEntity(user, user.username)

// Permissions modal
const isPermissionsModalOpen = ref(false)
const selectedUserForPermissions = ref<User | null>(null)
const selectedUserPermissions = ref<string[]>([])
const selectedUserRolePermissions = ref<string[]>([])

const openPermissionsModal = (user: User) => {
  selectedUserForPermissions.value = user
  selectedUserPermissions.value = user.all_permissions?.map((p) => p.name) ?? []
  selectedUserRolePermissions.value = user.role_permissions ?? []
  isPermissionsModalOpen.value = true
}

const closePermissionsModal = () => {
  isPermissionsModalOpen.value = false
  selectedUserForPermissions.value = null
  selectedUserPermissions.value = []
  selectedUserRolePermissions.value = []
}

const columns = computed<ColumnDef<User>[]>(() => {
  const cols: ColumnDef<User>[] = [
    idColumn(),
    fieldColumn('username', 'Usuario'),
    fieldColumn('dni', 'DNI'),
    fieldColumn('nombres', 'Nombres'),
    fieldColumn('apellidos', 'Apellidos'),
    fieldColumn('whatsapp_number', 'WhatsApp'),
    customColumn({
      accessorKey: 'roles',
      header: 'Rol',
      cell: (info) => getUserRoles(info.row.original),
    }),
    dateColumn('created_at', 'Fecha de creación'),
  ]

  if (hasPermission('editar usuarios') || hasPermission('eliminar usuarios')) {
    const isCurrentUser = (user: User) => user.id === authUserId.value

    cols.push({
      id: 'acciones',
      header: 'Acciones',
      cell: (info) => {
        const user = info.row.original
        const buttons: any[] = []

        if (hasPermission('editar usuarios')) {
          buttons.push(
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
          )
        }

        if (hasPermission('editar usuarios')) {
          buttons.push(
            h(
              BaseButton,
              {
                variant: 'ghost',
                size: 'sm',
                onClick: () => openPermissionsModal(user),
                class: 'text-blue-500 hover:text-blue-700',
              },
              () => h(LockIcon, { size: 18 }),
            ),
          )
        }

        if (hasPermission('eliminar usuarios') && !isCurrentUser(user)) {
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
    })
  }

  return cols
})

watch([() => form.nombres, () => form.apellidos], () => {
  if (!editingEntity.value) {
    form.password = generatePassword(form.nombres, form.apellidos)
    form.password_confirmation = form.password
  }
})
</script>
