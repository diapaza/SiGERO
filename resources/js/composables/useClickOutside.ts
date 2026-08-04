import { onMounted, onBeforeUnmount, type Ref } from 'vue'

/**
 * Composable que detecta clics fuera de un elemento.
 *
 * Registra un listener de `mousedown` en el documento y ejecuta `callback`
 * cuando el clic ocurre fuera del elemento referenciado. Útil para cerrar
 * dropdowns y menús al hacer clic fuera.
 *
 * @param targetRef Referencia al elemento contenedor.
 * @param callback Función a ejecutar al hacer clic fuera.
 */
export function useClickOutside(targetRef: Ref<HTMLElement | null>, callback: () => void) {
  function onClick(e: MouseEvent) {
    if (targetRef.value && !targetRef.value.contains(e.target as Node)) {
      callback()
    }
  }

  onMounted(() => document.addEventListener('mousedown', onClick))
  onBeforeUnmount(() => document.removeEventListener('mousedown', onClick))
}
