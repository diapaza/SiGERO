<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <!-- Profile Header -->
      <div
        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6"
      >
        <div class="flex flex-col items-center gap-5 xl:flex-row xl:items-center">
          <UserAvatar :name="user.name" :size="80" />
          <div class="text-center xl:text-left">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
              {{ user.name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</p>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <div class="border-b border-gray-200 dark:border-gray-800">
          <nav class="flex gap-1 px-6 overflow-x-auto" aria-label="Tabs">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              :class="[
                'py-4 px-1 border-b-2 text-sm font-medium transition-colors whitespace-nowrap',
                activeTab === tab.id
                  ? 'border-brand-500 text-brand-600 dark:border-brand-400 dark:text-brand-400'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
              ]"
              @click="activeTab = tab.id"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div class="p-6">
          <!-- Tab: Información Personal -->
          <div v-if="activeTab === 'profile'">
            <form @submit.prevent="submitProfile">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <BaseFormField
                  label="Nombre de usuario"
                  label-for="username"
                  :required="isAdmin"
                  :error="profileForm.errors.username"
                >
                  <BaseInput
                    id="username"
                    v-model="profileForm.username"
                    type="text"
                    :placeholder="
                      isAdmin
                        ? 'Ingrese el nombre de usuario'
                        : 'Solo administradores pueden modificar'
                    "
                    :state="profileForm.errors.username ? 'error' : 'default'"
                    class-name="w-full"
                    :disabled="!isAdmin"
                    @blur="isAdmin ? validateProfileField('username') : undefined"
                  />
                </BaseFormField>

                <BaseFormField
                  label="DNI"
                  label-for="dni"
                  :required="true"
                  :error="profileForm.errors.dni"
                >
                  <BaseInput
                    id="dni"
                    v-model="profileForm.dni"
                    type="text"
                    placeholder="Ingrese el DNI"
                    :state="profileForm.errors.dni ? 'error' : 'default'"
                    class-name="w-full"
                    @blur="validateProfileField('dni')"
                  />
                </BaseFormField>
              </div>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
                <BaseFormField
                  label="Nombres"
                  label-for="nombres"
                  :required="true"
                  :error="profileForm.errors.nombres"
                >
                  <BaseInput
                    id="nombres"
                    v-model="profileForm.nombres"
                    type="text"
                    placeholder="Ingrese los nombres"
                    :state="profileForm.errors.nombres ? 'error' : 'default'"
                    class-name="w-full"
                    @blur="validateProfileField('nombres')"
                  />
                </BaseFormField>

                <BaseFormField
                  label="Apellidos"
                  label-for="apellidos"
                  :required="true"
                  :error="profileForm.errors.apellidos"
                >
                  <BaseInput
                    id="apellidos"
                    v-model="profileForm.apellidos"
                    type="text"
                    placeholder="Ingrese los apellidos"
                    :state="profileForm.errors.apellidos ? 'error' : 'default'"
                    class-name="w-full"
                    @blur="validateProfileField('apellidos')"
                  />
                </BaseFormField>
              </div>

              <div class="mt-4">
                <BaseFormField
                  label="Número de WhatsApp"
                  label-for="whatsapp_number"
                  :error="profileForm.errors.whatsapp_number"
                >
                  <BaseInput
                    id="whatsapp_number"
                    v-model="profileForm.whatsapp_number"
                    type="text"
                    placeholder="Ingrese el número de WhatsApp"
                    :state="profileForm.errors.whatsapp_number ? 'error' : 'default'"
                    class-name="w-full sm:w-1/2"
                    @blur="validateProfileField('whatsapp_number')"
                  />
                </BaseFormField>
              </div>

              <div class="flex justify-end mt-6">
                <BaseButton type="submit" variant="primary" :disabled="profileForm.processing">
                  {{ profileForm.processing ? 'Guardando...' : 'Guardar cambios' }}
                </BaseButton>
              </div>
            </form>
          </div>

          <!-- Tab: Cambiar Contraseña -->
          <div v-if="activeTab === 'password'">
            <form @submit.prevent="submitPassword">
              <div class="max-w-md space-y-4">
                <BaseFormField
                  label="Contraseña actual"
                  label-for="current_password"
                  :required="true"
                  :error="passwordForm.errors.current_password"
                >
                  <BasePasswordInput
                    id="current_password"
                    v-model="passwordForm.current_password"
                    placeholder="Ingrese su contraseña actual"
                    class-name="w-full"
                    @blur="validatePasswordField('current_password')"
                  />
                </BaseFormField>

                <BaseFormField
                  label="Nueva contraseña"
                  label-for="password"
                  :required="true"
                  :error="passwordForm.errors.password"
                >
                  <BasePasswordInput
                    id="password"
                    v-model="passwordForm.password"
                    placeholder="Ingrese la nueva contraseña"
                    class-name="w-full"
                    @blur="validatePasswordField('password')"
                  />
                </BaseFormField>

                <BaseFormField
                  label="Confirmar nueva contraseña"
                  label-for="password_confirmation"
                  :required="true"
                >
                  <BasePasswordInput
                    id="password_confirmation"
                    v-model="passwordForm.password_confirmation"
                    placeholder="Confirme la nueva contraseña"
                    class-name="w-full"
                  />
                </BaseFormField>
              </div>

              <div class="flex justify-end mt-6">
                <BaseButton type="submit" variant="primary" :disabled="passwordForm.processing">
                  {{ passwordForm.processing ? 'Actualizando...' : 'Actualizar contraseña' }}
                </BaseButton>
              </div>
            </form>
          </div>

          <!-- Tab: Objetos Pendientes de Devolución -->
          <div v-if="activeTab === 'pending'">
            <div v-if="pendingReturns.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">
                No tiene objetos pendientes de devolución.
              </p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                  <tr>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                    >
                      Código
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                    >
                      Nombre
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                    >
                      Marca
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                    >
                      Categoría
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                    >
                      Fecha de Salida
                    </th>
                  </tr>
                </thead>
                <tbody
                  class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700"
                >
                  <tr
                    v-for="returnItem in pendingReturns"
                    :key="returnItem.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-800/50"
                  >
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ returnItem.objeto?.codigo }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                    >
                      {{ returnItem.objeto?.nombre }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                    >
                      {{ returnItem.objeto?.marca?.nombre ?? '-' }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                    >
                      {{ returnItem.objeto?.categoria?.nombre ?? '-' }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                    >
                      {{ formatDate(returnItem.fecha_hora) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import UserAvatar from '@/components/shared/UserAvatar.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { useValidation } from '@/composables/useValidation'
import { useFlashMessages } from '@/composables/useFlashMessages'
import type { User, PendingReturn } from '@/types/models'

const props = defineProps<{
  pendingReturns: PendingReturn[]
}>()

const pageTitle = ref('Mi Perfil')
const activeTab = ref<'profile' | 'password' | 'pending'>('profile')

const { pageProps } = useFlashMessages()
const user = computed<User>(() => pageProps.value.user)
const isAdmin = computed(() => {
  const roles = user.value.roles ?? []
  return roles.some((r) => r.name === 'Administrador')
})

const tabs = computed(() => {
  const baseTabs: Array<{ id: 'profile' | 'password' | 'pending'; label: string }> = [
    { id: 'profile', label: 'Información Personal' },
    { id: 'password', label: 'Cambiar Contraseña' },
  ]

  if (props.pendingReturns.length > 0) {
    baseTabs.push({
      id: 'pending',
      label: `Objetos Pendientes (${props.pendingReturns.length})`,
    })
  }

  return baseTabs
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-PE', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const profileForm = useForm({
  username: user.value.username,
  dni: user.value.dni,
  nombres: user.value.nombres,
  apellidos: user.value.apellidos,
  whatsapp_number: user.value.whatsapp_number ?? '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const profileLabels = {
  username: 'nombre de usuario',
  dni: 'DNI',
  nombres: 'nombres',
  apellidos: 'apellidos',
  whatsapp_number: 'número de WhatsApp',
}

const { validate: validateProfileBase, validateSingleField: validateProfileFieldBase } =
  useValidation(profileForm, 'profile', profileLabels)

const validateProfile = () => {
  const isValid = validateProfileBase()

  if (!isAdmin.value) {
    profileForm.clearErrors('username')
    return isValid && !profileForm.errors.username
  }

  return isValid
}

const validateProfileField = (field: string) => {
  if (field === 'username' && !isAdmin.value) {
    profileForm.clearErrors('username')
    return true
  }
  return validateProfileFieldBase(field)
}

const { validate: validatePassword, validateSingleField: validatePasswordField } = useValidation(
  passwordForm,
  'profilePassword',
  {
    current_password: 'contraseña actual',
    password: 'nueva contraseña',
  },
)

const submitProfile = () => {
  if (!validateProfile()) return

  profileForm.put(route('profile.update'))
}

const submitPassword = () => {
  if (!validatePassword()) return

  passwordForm.put(route('profile.password'), {
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}
</script>
