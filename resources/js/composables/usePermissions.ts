import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
  const page = usePage()

  const permissions = computed(() => (page.props.auth as any)?.user?.permissions ?? [])

  const hasPermission = (permission: string): boolean => {
    return permissions.value.includes(permission)
  }

  const hasAnyPermission = (perms: string[]): boolean => {
    return perms.some((p) => permissions.value.includes(p))
  }

  const hasAllPermissions = (perms: string[]): boolean => {
    return perms.every((p) => permissions.value.includes(p))
  }

  return {
    permissions,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
  }
}
