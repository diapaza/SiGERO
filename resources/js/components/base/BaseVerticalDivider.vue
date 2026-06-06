<template>
  <div :class="['w-px bg-gray-200 dark:bg-gray-800', sizeClass, className]" />
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  height?: 'sm' | 'md' | 'lg'
  className?: string
}>(), {
  height: 'md',
  className: '',
})

const sizeClass = computed(() => {
  const sizes = {
    sm: 'h-5',
    md: 'h-7',
    lg: 'h-10',
  }
  return sizes[props.height]
})
</script>
