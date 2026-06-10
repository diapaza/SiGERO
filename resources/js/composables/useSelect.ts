import { ref, computed, type Ref, type ComputedRef, watch } from 'vue'
import { useClickOutside } from './useClickOutside'

export { useClickOutside }

export interface SelectOption {
  value: string | number
  label: string
}

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

export function useSelectKeyboard(
  itemCount: Ref<number>,
  isOpen: Ref<boolean>,
  onEnter: (index: number) => void,
) {
  const highlightedIndex = ref(-1)

  const resetHighlight = () => {
    highlightedIndex.value = -1
  }

  watch(itemCount, () => {
    if (highlightedIndex.value >= itemCount.value) {
      highlightedIndex.value = itemCount.value - 1
    }
  })

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
