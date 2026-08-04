import { ref } from 'vue'

/**
 * Composable de estado de un modal.
 *
 * Expone `isOpen` y las acciones `open`, `close` y `toggle`. Se usa junto con
 * `BaseModal` para controlar su visibilidad (con `v-model:is-open`).
 *
 * @param initial Estado inicial del modal.
 */
export function useModal(initial = false) {
  const isOpen = ref<boolean>(initial)

  /** Abre el modal. */
  function open() {
    isOpen.value = true
  }

  /** Cierra el modal. */
  function close() {
    isOpen.value = false
  }

  /** Alterna el estado del modal. */
  function toggle() {
    isOpen.value = !isOpen.value
  }

  return { isOpen, open, close, toggle }
}

export default useModal
