<template>
  <div ref="selectRef" :class="['relative w-full', wrapperClass]">
    <!-- Trigger -->
    <div
      :id="id"
      :class="triggerClasses"
      :disabled="disabled"
      tabindex="0"
      role="combobox"
      :aria-expanded="isOpen"
      :aria-haspopup="'listbox'"
      @click="onTriggerClick"
      @focus="onFocus"
      @blur="onBlur"
      @keydown="handleKeyDown"
    >
      <span v-if="selectedOption" class="truncate flex-1">{{ selectedOption.label }}</span>
      <span v-else class="truncate flex-1 text-gray-400 dark:text-white/30">{{ placeholder }}</span>

      <span class="flex items-center gap-1 ml-auto">
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
          class="h-5 w-5 text-gray-700 dark:text-gray-400 transition-transform duration-200"
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
        <!-- Search Input -->
        <div v-if="searchable" class="p-2 border-b border-gray-200 dark:border-gray-700">
          <div class="relative">
            <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
            <input
              ref="searchInputRef"
              v-model="searchTerm"
              type="text"
              placeholder="Search..."
              class="w-full rounded-md border border-gray-300 bg-transparent pl-9 pr-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 focus:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30"
              @keydown="handleKeyDown"
            />
          </div>
        </div>

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
              :aria-selected="option.value === modelValue"
              @click="selectOption(option)"
              @mouseenter="highlightedIndex = index"
            >
              <span class="block truncate flex-1">{{ option.label }}</span>
              <CheckIcon
                v-if="option.value === modelValue"
                class="h-4 w-4 stroke-brand-500 shrink-0"
              />
            </li>

            <!-- Create option -->
            <li
              v-if="creatable && searchTerm && !hasExactMatch"
              :class="getCreateOptionClasses"
              role="option"
              @click="createOption"
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

<script setup lang="ts">
import { ref, computed, nextTick, watch } from 'vue'
import ChevronDownIcon from '@/icons/ChevronDownIcon.vue'
import CloseIcon from '@/icons/CloseIcon.vue'
import SearchIcon from '@/icons/SearchIcon.vue'
import CheckIcon from '@/icons/CheckIcon.vue'
import PlusIcon from '@/icons/PlusIcon.vue'
import { useFilteredOptions, useSelectKeyboard, useClickOutside } from '@/composables/useSelect'
import type { SelectOption } from '@/composables/useSelect'

const props = withDefaults(
  defineProps<{
    modelValue?: string | number | null
    options?: SelectOption[]
    creatable?: boolean
    searchable?: boolean
    placeholder?: string
    disabled?: boolean
    loading?: boolean
    clearable?: boolean
    state?: 'default' | 'error' | 'success'
    id?: string
    name?: string
    className?: string
    wrapperClass?: string
    createLabel?: string
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

const emits = defineEmits<{
  (e: 'update:modelValue', value: string | number | null): void
  (e: 'change', value: string | number | null): void
  (e: 'focus', event: FocusEvent): void
  (e: 'blur', event: FocusEvent): void
  (e: 'enter', event: KeyboardEvent): void
  (e: 'search', term: string): void
  (e: 'open'): void
  (e: 'close'): void
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

const triggerClasses = computed(() => [
  'h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 flex items-center',
  stateClasses[props.state],
  props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
  props.className,
])

const getOptionClasses = (option: SelectOption, index: number) => [
  'relative flex items-center w-full px-3 py-2.5 text-sm cursor-pointer transition-colors',
  highlightedIndex.value === index
    ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400'
    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]',
  option.value === props.modelValue ? 'font-medium' : '',
]

const getCreateOptionClasses = computed(() => [
  'relative flex items-center w-full px-3 py-2.5 text-sm cursor-pointer transition-colors border-t border-gray-200 dark:border-gray-700',
  highlightedIndex.value === filteredOptions.length
    ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400'
    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]',
])

const isOpen = ref(false)
const searchTerm = ref('')
const selectRef = ref<HTMLElement | null>(null)
const searchInputRef = ref<HTMLInputElement | null>(null)

const optionsRef = computed(() => props.options)

const filteredOptions = useFilteredOptions(optionsRef, searchTerm, props.filterBy)

const selectedOption = computed(() => {
  const val = props.modelValue
  if (val === null || val === undefined || val === '') return null
  return props.options.find((opt) => opt.value === val) || null
})

const hasExactMatch = computed(() => {
  if (!searchTerm.value) return true
  const term = searchTerm.value.toLowerCase()
  return props.options.some((opt) => opt.label.toLowerCase() === term)
})

const itemCount = computed(() => {
  let count = filteredOptions.value.length
  if (props.creatable && searchTerm.value && !hasExactMatch.value) {
    count += 1
  }
  return count
})

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

useClickOutside(selectRef, () => {
  closeDropdown()
})

watch(searchTerm, (val) => {
  emits('search', val)
  if (props.searchable && !isOpen.value && val) {
    isOpen.value = true
    nextTick(() => searchInputRef.value?.focus())
  }
  resetHighlight()
})

function onTriggerClick() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    emits('open')
    if (props.searchable) {
      nextTick(() => searchInputRef.value?.focus())
    }
  } else {
    emits('close')
    searchTerm.value = ''
    resetHighlight()
  }
}

function closeDropdown() {
  if (!isOpen.value) return
  isOpen.value = false
  searchTerm.value = ''
  resetHighlight()
  emits('close')
}

function selectOption(option: SelectOption) {
  emits('update:modelValue', option.value)
  emits('change', option.value)
  closeDropdown()
}

function createOption() {
  if (!searchTerm.value) return
  emits('create', searchTerm.value)
  closeDropdown()
}

function clear() {
  emits('update:modelValue', null)
  emits('change', null)
}

function onFocus(e: FocusEvent) {
  emits('focus', e)
}

function onBlur(e: FocusEvent) {
  emits('blur', e)
}
</script>
