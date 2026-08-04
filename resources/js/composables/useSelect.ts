import { ref, computed, type Ref, type ComputedRef, watch } from 'vue'
import { useClickOutside } from './useClickOutside'

export { useClickOutside }

/** Opción de un selector. */
export interface SelectOption {
  value: string | number
  label: string
}

/**
 * Filtra las opciones según el término de búsqueda.
 *
 * Si no hay término, devuelve todas las opciones. Si se provee `filterBy`,
 * se usa en lugar del filtro por defecto (por etiqueta).
 *
 * @param options Ref de las opciones.
 * @param searchTerm Ref del término de búsqueda.
 * @param filterBy Predicado opcional de filtrado.
 * @returns ComputedRef con las opciones filtradas.
 */
export function useFilteredOptions(
  options: Ref<SelectOption[]>,
  searchTerm: Ref<string>,
  filterBy?: (option: SelectOption, search: string) => boolean,
): ComputedRef<SelectOption[]> {
  return computed(() => {
    if (!searchTerm.value) return options.value
    const term = searchTerm.value.toLowerCase()
    return options.value.filter((opt) => {
      if (filterBy) return filterBy(opt, searchTerm.value)
      return opt.label.toLowerCase().includes(term)
    })
  })
}

/**
 * Navegación por teclado del selector (flechas, Enter, Escape y Tab).
 *
 * @param itemCount Ref con la cantidad de ítems visibles (incluye "crear").
 * @param isOpen Ref que controla si el dropdown está abierto.
 * @param onEnter Callback que se ejecuta al presionar Enter/Tab con un ítem resaltado.
 */
export function useSelectKeyboard(
  itemCount: Ref<number>,
  isOpen: Ref<boolean>,
  onEnter: (index: number) => void,
) {
  /** Índice del ítem resaltado (-1 si ninguno). */
  const highlightedIndex = ref(-1)

  /** Reinicia el resaltado a -1. */
  const resetHighlight = () => {
    highlightedIndex.value = -1
  }

  // Mantiene el resaltado dentro del rango cuando cambia el conteo de ítems.
  watch(itemCount, () => {
    if (highlightedIndex.value >= itemCount.value) {
      highlightedIndex.value = itemCount.value - 1
    }
  })

  /** Maneja los eventos de teclado del selector. */
  function handleKeyDown(e: KeyboardEvent) {
    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault()
        if (!isOpen.value) {
          isOpen.value = true
          return
        }
        if (itemCount.value === 0) return
        highlightedIndex.value =
          highlightedIndex.value < itemCount.value - 1 ? highlightedIndex.value + 1 : 0
        break
      case 'ArrowUp':
        e.preventDefault()
        if (itemCount.value === 0) return
        highlightedIndex.value =
          highlightedIndex.value > 0 ? highlightedIndex.value - 1 : itemCount.value - 1
        break
      case 'Enter':
        e.preventDefault()
        if (highlightedIndex.value >= 0 && highlightedIndex.value < itemCount.value) {
          onEnter(highlightedIndex.value)
        }
        break
      case 'Escape':
        e.preventDefault()
        isOpen.value = false
        resetHighlight()
        break
      case 'Tab':
        if (isOpen.value) {
          if (highlightedIndex.value >= 0 && highlightedIndex.value < itemCount.value) {
            onEnter(highlightedIndex.value)
          }
          isOpen.value = false
          resetHighlight()
        }
        break
    }
  }

  return { highlightedIndex, resetHighlight, handleKeyDown }
}
