<template>
  <BaseModal
    :is-open="isOpen"
    :title="`Permisos - ${user?.username ?? ''}`"
    size="lg"
    @close="$emit('close')"
  >
    <template #body>
      <div v-if="loading" class="py-8 text-center text-gray-500">Cargando permisos...</div>

      <div v-else class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Asigne permisos directos a este usuario. Los permisos de los roles asignados se aplican
          automáticamente y se muestran bloqueados.
        </p>

        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
          <label
            v-for="permission in allPermissions"
            :key="permission.id"
            :class="[
              'flex items-center gap-2 rounded-lg border p-3',
              isRolePermission(permission.name)
                ? 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/40 cursor-not-allowed'
                : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50 cursor-pointer',
            ]"
          >
            <input
              v-model="selectedPermissions"
              type="checkbox"
              :value="permission.name"
              :disabled="isRolePermission(permission.name)"
              class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ permission.name }}</span>
            <span
              v-if="isRolePermission(permission.name)"
              class="ml-auto text-xs text-gray-400 dark:text-gray-500"
            >
              vía rol
            </span>
          </label>
        </div>

        <div v-if="allPermissions.length === 0" class="py-4 text-center text-gray-500">
          No hay permisos disponibles. Cree permisos en la configuración del sistema.
        </div>
      </div>
    </template>

    <template #actions>
      <BaseButton variant="outline" @click="$emit('close')"> Cancelar </BaseButton>
      <BaseButton variant="primary" :disabled="saving" @click="savePermissions">
        {{ saving ? 'Guardando...' : 'Guardar permisos' }}
      </BaseButton>
    </template>
  </BaseModal>
</template>

/** * Modal para gestionar los permisos directos de un usuario. * * Muestra todos los permisos del
sistema con casillas de verificación; los * permisos heredados de los roles del usuario aparecen
bloqueados ("vía rol"). * Al guardar, sincroniza únicamente los permisos directos mediante Inertia.
*/
<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import type { User, Permission } from '@/types/models'

const props = defineProps<{
  /** Controla la visibilidad del modal. */
  isOpen: boolean
  /** Usuario cuyos permisos se gestionan. */
  user: User | null
  /** Catálogo completo de permisos disponibles. */
  allPermissions: Permission[]
  /** Permisos directos actualmente asignados al usuario. */
  userPermissions: string[]
  /** Permisos heredados de los roles del usuario (bloqueados). */
  rolePermissions?: string[]
}>()

// Emite:
/** Se emite al cerrar el modal (cancelación o tras guardar). */
const emit = defineEmits<{
  close: []
}>()

// Permisos seleccionados en el modal, estado de guardado y de carga.
const selectedPermissions = ref<string[]>([])
const saving = ref(false)
const loading = ref(false)

/** Indica si un permiso proviene de un rol asignado al usuario. */
const isRolePermission = (name: string): boolean => {
  return (props.rolePermissions ?? []).includes(name)
}

/** Al cambiar el usuario, carga sus permisos directos actuales. */
watch(
  () => props.user,
  (newUser) => {
    if (newUser) {
      selectedPermissions.value = [...props.userPermissions]
    }
  },
)

/** Al abrir el modal, precarga los permisos directos del usuario. */
watch(
  () => props.isOpen,
  (isOpen) => {
    if (isOpen && props.user) {
      loading.value = true
      selectedPermissions.value = [...props.userPermissions]
      loading.value = false
    }
  },
)

/**
 * Guarda los permisos directos seleccionados (excluyendo los de rol) sincronizando
 * el usuario mediante el router de Inertia.
 */
const savePermissions = () => {
  if (!props.user) return

  saving.value = true

  // Solo se sincronizan los permisos directos; los del rol se mantienen intactos.
  const directPermissions = selectedPermissions.value.filter((name) => !isRolePermission(name))

  router.put(
    route('users.permissions.sync', props.user.id),
    { permissions: directPermissions },
    {
      preserveScroll: true,
      onSuccess: () => {
        emit('close')
        router.reload({ only: ['users'] })
      },
      onFinish: () => {
        saving.value = false
      },
    },
  )
}
</script>
