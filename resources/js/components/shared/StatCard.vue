<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
      <component :is="icon" class="fill-gray-800 dark:fill-white/90" />
    </div>
    <div class="flex items-end justify-between mt-5">
      <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ label }}</span>
        <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ value }}</h4>
      </div>
      <TrendIndicator v-if="trend" v-bind="trend" />
    </div>
  </div>
</template>

<script setup lang="ts">
import TrendIndicator from '@/components/shared/TrendIndicator.vue'

defineProps<{
  icon: object
  label: string
  value: string | number
  trend?: {
    direction?: 'up' | 'down'
    percentage?: string | number
    color?: 'red' | 'green'
  }
}>()
</script>
