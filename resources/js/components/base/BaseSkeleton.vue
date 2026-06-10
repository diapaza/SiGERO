<template>
  <div :class="['animate-pulse', wrapperClass]">
    <div v-if="variant === 'text'" :class="['bg-gray-200 dark:bg-gray-700 rounded', sizeClass]" />
    <div
      v-else-if="variant === 'circular'"
      :class="['bg-gray-200 dark:bg-gray-700 rounded-full', sizeClass]"
    />
    <div
      v-else-if="variant === 'rectangular'"
      :class="['bg-gray-200 dark:bg-gray-700 rounded-lg', sizeClass]"
    />
    <div v-else :class="['bg-gray-200 dark:bg-gray-700 rounded-lg', sizeClass]" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  variant?: 'text' | 'circular' | 'rectangular'
  width?: string | number
  height?: string | number
  wrapperClass?: string
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'rectangular',
  width: '100%',
  height: 20,
  wrapperClass: '',
})

const sizeClass = computed(() => ({
  'h-4 w-full': props.variant === 'text' && props.height === 20,
  'w-10 h-10': props.variant === 'circular',
}))
</script>
