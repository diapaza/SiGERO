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

<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import type { User, Permission } from '@/types/models'

const props = defineProps<{
  isOpen: boolean
  user: User | null
  allPermissions: Permission[]
  userPermissions: string[]
  rolePermissions?: string[]
}>()

const emit = defineEmits<{
  close: []
}>()

const selectedPermissions = ref<string[]>([])
const saving = ref(false)
const loading = ref(false)

const isRolePermission = (name: string): boolean => {
  return (props.rolePermissions ?? []).includes(name)
}

watch(
  () => props.user,
  (newUser) => {
    if (newUser) {
      selectedPermissions.value = [...props.userPermissions]
    }
  },
)

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
