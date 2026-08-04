import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Composable de acceso a las props de la página Inertia.
 *
 * Devuelve `pageProps`, un `ComputedRef` con todas las props que el servidor
 * envía a la vista actual (incluidas las compartidas). Es el puente entre los
 * datos del backend y el frontend en las vistas.
 */
export function useFlashMessages() {
  const page = usePage()
  const pageProps = computed(() => page.props as any)

  return { pageProps }
}
