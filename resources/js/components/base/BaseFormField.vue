<template>
  <div :class="['space-y-1.5', wrapperClass]">
    <label
      v-if="label || $slots.label"
      :for="labelFor"
      :class="[
        'block text-sm font-medium',
        disabled
          ? 'text-gray-300 dark:text-white/15'
          : 'text-gray-700 dark:text-gray-400',
        labelClass,
      ]"
    >
      <slot name="label">
        {{ label }}
      </slot>
      <span v-if="required" class="text-error-500 ml-0.5">*</span>
    </label>

    <slot />

    <p v-if="error" class="text-theme-xs text-error-500">
      {{ error }}
    </p>
    <p v-else-if="hint" class="text-theme-xs text-gray-500 dark:text-gray-400">
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  label?: string
  labelFor?: string
  required?: boolean
  error?: string
  hint?: string
  disabled?: boolean
  labelClass?: string
  wrapperClass?: string
}>(), {
  label: '',
  required: false,
  disabled: false,
  labelClass: '',
  wrapperClass: '',
})
</script>
