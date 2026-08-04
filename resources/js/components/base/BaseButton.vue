<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400',
      sizeClasses[size],
      variantClasses[variant],
      className,
      { 'cursor-not-allowed opacity-50': disabled },
    ]"
    :disabled="disabled"
    @click="handleClick"
  >
    <span v-if="$slots.start || startIcon" class="flex items-center">
      <slot name="start">
        <component :is="startIcon" v-if="startIcon" />
      </slot>
    </span>
    <slot />
    <span v-if="$slots.end || endIcon" class="flex items-center">
      <slot name="end">
        <component :is="endIcon" v-if="endIcon" />
      </slot>
    </span>
  </button>
</template>

<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    type?: 'button' | 'submit' | 'reset'
    size?: 'sm' | 'md' | 'lg'
    variant?: 'primary' | 'outline' | 'secondary' | 'ghost' | 'danger'
    disabled?: boolean
    className?: string
    startIcon?: object
    endIcon?: object
  }>(),
  {
    type: 'button',
    size: 'md',
    variant: 'primary',
    disabled: false,
    className: '',
  },
)

const emits = defineEmits<{
  (e: 'click', event: MouseEvent): void
}>()

const sizeClasses = {
  sm: 'px-4 py-2 text-sm',
  md: 'px-5 py-3 text-sm',
  lg: 'px-6 py-3.5 text-base',
}

const variantClasses = {
  primary: 'bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:bg-brand-300',
  outline:
    'bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-white/[0.03]',
  secondary:
    'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-white/90 dark:hover:bg-gray-700',
  ghost: 'bg-transparent text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
  danger: 'bg-error-500 text-white hover:bg-error-600 disabled:bg-error-300',
}

const handleClick = (event: MouseEvent) => {
  if (!props.disabled) emits('click', event)
}
</script>
