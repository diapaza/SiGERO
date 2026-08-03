import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useFlashMessages() {
  const page = usePage()
  const pageProps = computed(() => page.props as any)

  return { pageProps }
}
