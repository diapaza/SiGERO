<template>
  <div ref="selectRef" :class="['relative w-full', wrapperClass]">
    <!-- Input -->
    <div class="relative">
      <input
        :id="id"
        ref="inputRef"
        type="text"
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="inputClasses"
        autocomplete="off"
        @input="onInput"
        @focus="onFocus"
        @blur="onBlur"
        @keydown="handleKeyDown"
        @click="onTriggerClick"
      />
      <span class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
        <button
          v-if="clearable && modelValue !== null && modelValue !== undefined && modelValue !== ''"
          type="button"
          tabindex="-1"
          class="p-0.5 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-hidden"
          @click.stop="clear"
        >
          <CloseIcon class="h-4 w-4" />
        </button>
        <ChevronDownIcon
          class="h-5 w-5 text-gray-700 dark:text-gray-400 transition-transform duration-200 pointer-events-none"
          :class="{ 'rotate-180': isOpen }"
        />
      </span>
    </div>

    <!-- Dropdown -->
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-theme-xs border border-gray-200 dark:bg-gray-900 dark:border-gray-700"
      >
        <!-- Options List -->
        <ul class="max-h-60 overflow-y-auto py-1" role="listbox">
          <li v-if="loading" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
            Loading...
          </li>

          <template v-else>
            <li
              v-for="(option, index) in filteredOptions"
              :key="option.value"
              :class="getOptionClasses(option, index)"
              role="option"
              :aria-selected="isSelected(option.value)"
              @mousedown.prevent="selectOption(option)"
              @mouseenter="highlightedIndex = index"
            >
              <span class="block truncate flex-1">{{ option.label }}</span>
              <CheckIcon
                v-if="isSelected(option.value)"
                class="h-4 w-4 stroke-brand-500 shrink-0"
              />
            </li>

            <!-- Create option -->
            <li
              v-if="creatable && searchTerm && !hasExactMatch"
              :class="getCreateOptionClasses"
              role="option"
              @mousedown.prevent="createOption"
              @mouseenter="highlightedIndex = filteredOptions.length"
            >
              <PlusIcon class="h-4 w-4 mr-2 shrink-0" />
              <span class="truncate">{{ createLabel.replace('{text}', searchTerm) }}</span>
            </li>

            <!-- No results -->
            <li
              v-if="filteredOptions.length === 0 && !(creatable && searchTerm && !hasExactMatch)"
              class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              No results found
            </li>
          </template>
        </ul>
      </div>
    </transition>
  </div>
</template>

/** * Selector desplegable con búsqueda reutilizable. * * Ofrece búsqueda, navegación por teclado,
creación de opciones, carga * asíncrona y limpieza. Expone el valor seleccionado mediante `v-model`
y * varios eventos para abrir/cerrar, buscar, crear y cambios de selección. */
<script setup lang="ts">
import { ref, computed, nextTick, watch } from 'vue'
import ChevronDownIcon from '@/icons/ChevronDownIcon.vue'
import CloseIcon from '@/icons/CloseIcon.vue'
import CheckIcon from '@/icons/CheckIcon.vue'
import PlusIcon from '@/icons/PlusIcon.vue'
import { useFilteredOptions, useSelectKeyboard, useClickOutside } from '@/composables/useSelect'
import type { SelectOption } from '@/composables/useSelect'

const props = withDefaults(
  defineProps<{
    /** Valor seleccionado (v-model). */
    modelValue?: string | number | null
    /** Lista de opciones disponibles para seleccionar. */
    options?: SelectOption[]
    /** Permite crear nuevas opciones a partir del texto buscado. */
    creatable?: boolean
    /** Habilita la búsqueda dentro de las opciones. */
    searchable?: boolean
    /** Texto de marcador de posición del input. */
    placeholder?: string
    /** Deshabilita el selector e impide su interacción. */
    disabled?: boolean
    /** Muestra un estado de carga en la lista de opciones. */
    loading?: boolean
    /** Muestra un botón para limpiar el valor seleccionado. */
    clearable?: boolean
    /** Estado visual del campo (default, error o success). */
    state?: 'default' | 'error' | 'success'
    /** Identificador HTML del input. */
    id?: string
    /** Atributo `name` del input. */
    name?: string
    /** Clases CSS adicionales aplicadas al input. */
    className?: string
    /** Clases CSS adicionales aplicadas al contenedor. */
    wrapperClass?: string
    /** Etiqueta mostrada en la opción de crear, con `{text}` como marcador. */
    createLabel?: string
    /** Función personalizada para filtrar las opciones por el término buscado. */
    filterBy?: (option: SelectOption, search: string) => boolean
  }>(),
  {
    modelValue: null,
    options: () => [],
    creatable: false,
    searchable: true,
    placeholder: 'Select...',
    disabled: false,
    loading: false,
    clearable: false,
    state: 'default',
    className: '',
    wrapperClass: '',
    createLabel: 'Create "{text}"',
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza el valor seleccionado (v-model). */
  (e: 'update:modelValue', value: string | number | null): void
  /** Se emite cuando cambia la selección. */
  (e: 'change', value: string | number | null): void
  /** Se emite al enfocar el input. */
  (e: 'focus', event: FocusEvent): void
  /** Se emite al perder el foco del input. */
  (e: 'blur', event: FocusEvent): void
  /** Se emite al presionar Enter sobre el input. */
  (e: 'enter', event: KeyboardEvent): void
  /** Se emite con el término de búsqueda al escribir. */
  (e: 'search', term: string): void
  /** Se emite al abrir el desplegable. */
  (e: 'open'): void
  /** Se emite al cerrar el desplegable. */
  (e: 'close'): void
  /** Se emite al crear una nueva opción con el texto ingresado. */
  (e: 'create', value: string): void
}>()

const stateClasses: Record<string, string> = {
  default:
    'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800',
  error:
    'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800',
  success:
    'border-success-300 focus:border-success-300 focus:ring-success-500/10 dark:border-success-700 dark:focus:border-success-800',
}

const inputClasses = computed(() => [
  'h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
  stateClasses[props.state],
  props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-text',
  props.className,
])

/** Devuelve las clases CSS de una opción según su índice y estado de selección. */
const getOptionClasses = (option: SelectOption, index: number) => [
  'relative flex items-center w-full px-3 py-2.5 text-sm cursor-pointer transition-colors',
  highlightedIndex.value === index
    ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400'
    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]',
  isSelected(option.value) ? 'font-medium' : '',
]

/** Clases CSS de la opción para crear un nuevo valor. */
const getCreateOptionClasses = computed(() => [
  'relative flex items-center w-full px-3 py-2.5 text-sm cursor-pointer transition-colors border-t border-gray-200 dark:border-gray-700',
  highlightedIndex.value === filteredOptions.value.length
    ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400'
    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]',
])

/** Estado interno del desplegable (abierto/cerrado). */
const isOpen = ref(false)
/** Indica si el usuario está editando el término de búsqueda. */
const isEditing = ref(false)
/** Término de búsqueda actual. */
const searchTerm = ref('')
/** Referencia al contenedor raíz del componente. */
const selectRef = ref<HTMLElement | null>(null)
/** Referencia al input del selector. */
const inputRef = ref<HTMLInputElement | null>(null)
/** Lista de opciones proveniente de la prop `options`. */
const optionsRef = computed(() => props.options)

/** Indica si un valor dado es el actualmente seleccionado. */
const isSelected = (value: string | number): boolean => {
  if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
    return false
  }
  return String(value) === String(props.modelValue)
}

/** Opciones filtradas según el término de búsqueda (o el `filterBy` dado). */
const filteredOptions = useFilteredOptions(optionsRef, searchTerm, props.filterBy)

/** Opción seleccionada en la lista de opciones, o null si no hay coincidencia. */
const selectedOption = computed(() => {
  const val = props.modelValue
  if (val === null || val === undefined || val === '') return null
  return props.options.find((opt) => isSelected(opt.value)) || null
})

/** Valor mostrado en el input (etiqueta seleccionada o término en edición). */
const displayValue = computed(() => {
  if (isEditing.value) return searchTerm.value
  if (selectedOption.value) return selectedOption.value.label
  return ''
})

/** Indica si el término buscado coincide exactamente con alguna etiqueta. */
const hasExactMatch = computed(() => {
  if (!searchTerm.value) return true
  const term = searchTerm.value.toLowerCase()
  return props.options.some((opt) => opt.label.toLowerCase() === term)
})

/** Total de elementos del desplegable (incluye la opción de crear si aplica). */
const itemCount = computed(() => {
  let count = filteredOptions.value.length
  if (props.creatable && searchTerm.value && !hasExactMatch.value) {
    count += 1
  }
  return count
})

/** Gestiona la navegación por teclado y el índice de opción resaltada. */
const { highlightedIndex, resetHighlight, handleKeyDown } = useSelectKeyboard(
  itemCount,
  isOpen,
  (index: number) => {
    if (index < filteredOptions.value.length) {
      selectOption(filteredOptions.value[index])
    } else if (props.creatable && searchTerm.value && !hasExactMatch.value) {
      createOption()
    }
  },
)

/** Cierra el desplegable al hacer clic fuera del componente. */
useClickOutside(selectRef, () => {
  closeDropdown()
})

/** Emite el término de búsqueda y abre el desplegable si hay texto. */
watch(searchTerm, (val) => {
  emits('search', val)
  if (!isOpen.value && val) {
    isOpen.value = true
  }
  resetHighlight()
})

/** Actualiza el término de búsqueda desde la escritura en el input. */
function onInput(e: Event) {
  const target = e.target as HTMLInputElement
  searchTerm.value = target.value
  if (!isOpen.value && target.value) {
    isOpen.value = true
  }
}

/** Abre el desplegable en modo edición al hacer clic en el input. */
function onTriggerClick() {
  if (props.disabled) return
  if (!isOpen.value) {
    isEditing.value = true
    isOpen.value = true
    emits('open')
  }
}

/** Emite `focus` y abre el desplegable al enfocar el input. */
function onFocus(e: FocusEvent) {
  emits('focus', e)
  isEditing.value = true
  if (!isOpen.value) {
    isOpen.value = true
    emits('open')
  }
}

/** Emite el evento `blur` al perder el foco del input. */
function onBlur(e: FocusEvent) {
  emits('blur', e)
}

/** Cierra el desplegable y restablece el estado de edición y búsqueda. */
function closeDropdown() {
  if (!isOpen.value) return
  isOpen.value = false
  isEditing.value = false
  searchTerm.value = ''
  resetHighlight()
  emits('close')
}

/** Selecciona una opción, actualiza el v-model y cierra el desplegable. */
function selectOption(option: SelectOption) {
  emits('update:modelValue', option.value)
  emits('change', option.value)
  isEditing.value = false
  searchTerm.value = ''
  isOpen.value = false
  resetHighlight()
  emits('close')
  nextTick(() => {
    inputRef.value?.blur()
  })
}

/** Crea una nueva opción con el término buscado y cierra el desplegable. */
function createOption() {
  if (!searchTerm.value) return
  emits('create', searchTerm.value)
  isEditing.value = false
  searchTerm.value = ''
  isOpen.value = false
  resetHighlight()
  emits('close')
}

/** Limpia el valor seleccionado y devuelve el foco al input. */
function clear() {
  emits('update:modelValue', null)
  emits('change', null)
  isEditing.value = false
  searchTerm.value = ''
  nextTick(() => {
    inputRef.value?.focus()
  })
}
</script>
