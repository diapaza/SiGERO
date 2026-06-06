<template>
  <div :class="wrapperClass">
    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
      {{ label }}
    </p>
    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
      <slot>{{ value }}</slot>
    </p>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  label: string
  value?: string | number
  wrapperClass?: string
}>(), {
  value: '',
  wrapperClass: '',
})
</script>
