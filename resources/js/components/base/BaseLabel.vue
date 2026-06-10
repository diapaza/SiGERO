<template>
  <label
    v-if="label || $slots.default"
    :for="labelFor"
    :class="[
      'mb-1.5 block text-sm font-medium',
      disabled ? 'text-gray-300 dark:text-white/15' : 'text-gray-700 dark:text-gray-400',
      labelClass,
    ]"
  >
    <slot>{{ label }}</slot>
    <span v-if="required" class="text-error-500 ml-0.5">*</span>
  </label>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    label?: string
    labelFor?: string
    required?: boolean
    disabled?: boolean
    labelClass?: string
  }>(),
  {
    label: '',
    required: false,
    disabled: false,
    labelClass: '',
  },
)
</script>
