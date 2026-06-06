<template>
  <span :class="['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-sm font-medium', colorClasses]">
    <TrendArrowIcon :direction="direction" :color="computedColor" :size="12" />
    {{ percentage }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import TrendArrowIcon from '@/icons/TrendArrowIcon.vue'

const props = withDefaults(defineProps<{
  direction?: 'up' | 'down'
  percentage?: string | number
  color?: 'red' | 'green'
}>(), {
  direction: 'up',
  percentage: '',
})

const computedColor = computed(() => {
  if (props.color) return props.color
  return props.direction === 'up' ? 'green' : 'red'
})

const colorClasses = computed(() => {
  const isGreen = computedColor.value === 'green'
  return isGreen
    ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
    : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'
})
</script>
