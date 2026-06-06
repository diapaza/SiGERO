<template>
  <div class="relative" ref="multiSelectRef">
    <div
      @click="toggleDropdown"
      class="dark:bg-dark-900 h-11 flex items-center w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
      :class="{ 'text-gray-800 dark:text-white/90': isOpen }"
    >
      <span v-if="selectedItems.length === 0" class="text-gray-400"> Select items... </span>
      <div class="flex flex-wrap items-center flex-auto gap-2">
        <div
          v-for="item in selectedItems"
          :key="item.value"
          class="group flex items-center justify-center h-[30px] rounded-full border-[0.7px] border-transparent bg-gray-100 py-1 pl-2.5 pr-2 text-sm text-gray-800 hover:border-gray-200 dark:bg-gray-800 dark:text-white/90 dark:hover:border-gray-800"
        >
          <span>{{ item.label }}</span>
          <button
            @click.stop="removeItem(item)"
            class="pl-2 text-gray-500 cursor-pointer group-hover:text-gray-400 dark:text-gray-400"
            aria-label="Remove item"
          >
            <CloseIcon class="fill-current" width="14" height="14" />
          </button>
        </div>
      </div>
      <ChevronDownIcon
        class="ml-auto"
        :class="{ 'transform rotate-180': isOpen }"
      />
    </div>
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
        class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-sm dark:bg-gray-900"
      >
        <ul
          class="overflow-y-auto divide-y divide-gray-200 custom-scrollbar max-h-60 dark:divide-gray-800"
          role="listbox"
          aria-multiselectable="true"
        >
          <li
            v-for="item in props.options"
            :key="item.value"
            @click="toggleItem(item)"
            class="relative flex items-center w-full px-3 py-2 border-transparent cursor-pointer first:rounded-t-lg last:rounded-b-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{ 'bg-gray-50 dark:bg-white/[0.03]': isSelected(item) }"
            role="option"
            :aria-selected="isSelected(item)"
          >
            <span class="grow">{{ item.label }}</span>
            <svg
              v-if="isSelected(item)"
              class="w-5 h-5 text-gray-400 dark:text-gray-300"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              ></path>
            </svg>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import CloseIcon from '@/icons/CloseIcon.vue'
import ChevronDownIcon from '@/icons/ChevronDownIcon.vue'

interface SelectOption {
  value: string | number
  label: string
}

const props = withDefaults(defineProps<{
  options: SelectOption[]
  modelValue?: SelectOption[]
}>(), {
  modelValue: () => [],
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: SelectOption[]): void
}>()

const isOpen = ref(false)
const selectedItems = ref(props.modelValue)
const multiSelectRef = ref(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const toggleItem = (item) => {
  const index = selectedItems.value.findIndex((selected) => selected.value === item.value)
  if (index === -1) {
    selectedItems.value.push(item)
  } else {
    selectedItems.value.splice(index, 1)
  }
  emit('update:modelValue', selectedItems.value)
}

const removeItem = (item) => {
  const index = selectedItems.value.findIndex((selected) => selected.value === item.value)
  if (index !== -1) {
    selectedItems.value.splice(index, 1)
    emit('update:modelValue', selectedItems.value)
  }
}

const isSelected = (item) => {
  return selectedItems.value.some((selected) => selected.value === item.value)
}

const handleClickOutside = (event) => {
  if (multiSelectRef.value && !multiSelectRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
