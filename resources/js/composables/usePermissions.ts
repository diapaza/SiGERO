import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Composable de acceso a los permisos del usuario autenticado.
 *
 * Lee la prop compartida `auth.user.permissions` de Inertia (permisos
 * efectivos: roles + directos) y expone helpers para consultarlos en
 * templates y lógica de renderizado condicional.
 */
export function usePermissions() {
  const page = usePage()

  /** Permisos efectivos del usuario autenticado. */
  const permissions = computed(() => (page.props.auth as any)?.user?.permissions ?? [])

  /**
   * Comprueba si el usuario tiene un permiso concreto.
   *
   * @param permission Nombre del permiso.
   */
  const hasPermission = (permission: string): boolean => {
    return permissions.value.includes(permission)
  }

  /**
   * Comprueba si el usuario tiene al menos uno de los permisos dados.
   *
   * @param perms Lista de permisos.
   */
  const hasAnyPermission = (perms: string[]): boolean => {
    return perms.some((p) => permissions.value.includes(p))
  }

  /**
   * Comprueba si el usuario tiene todos los permisos dados.
   *
   * @param perms Lista de permisos.
   */
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
