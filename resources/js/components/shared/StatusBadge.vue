<template>
  <span :class="[baseClasses, colorClasses]">
    {{ label || status }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  status: string
  label?: string
  variant?: 'light' | 'solid'
  size?: 'sm' | 'md'
  map?: Record<string, { light: string; solid: string }>
}>(), {
  variant: 'light',
  size: 'sm',
})

const defaultColorMap: Record<string, { light: string; solid: string }> = {
  success: {
    light: 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
    solid: 'bg-success-500 text-white',
  },
  error: {
    light: 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    solid: 'bg-error-500 text-white',
  },
  warning: {
    light: 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
    solid: 'bg-warning-500 text-white',
  },
  info: {
    light: 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
    solid: 'bg-blue-light-500 text-white',
  },
  primary: {
    light: 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400',
    solid: 'bg-brand-500 text-white',
  },
  neutral: {
    light: 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
    solid: 'bg-gray-500 text-white',
  },
}

const colorMap = computed(() => props.map || defaultColorMap)

const colorClasses = computed(() => {
  const entry = colorMap.value[props.status]
  if (entry) return entry[props.variant]
  return defaultColorMap.neutral[props.variant]
})

const baseClasses = computed(() => [
  'inline-flex items-center justify-center rounded-full font-medium',
  props.size === 'sm' ? 'px-2 py-0.5 text-theme-xs' : 'px-2.5 py-0.5 text-sm',
])
</script>
