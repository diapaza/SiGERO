<template>
  <span :class="[baseStyles, sizeStyles[size], colorStyles, className]">
    <span v-if="$slots.start || startIcon" class="mr-1 inline-flex items-center">
      <slot name="start">
        <component :is="startIcon" v-if="startIcon" />
      </slot>
    </span>
    <slot />
    <span v-if="$slots.end || endIcon" class="ml-1 inline-flex items-center">
      <slot name="end">
        <component :is="endIcon" v-if="endIcon" />
      </slot>
    </span>
  </span>
</template>

/** * Insignia o etiqueta de estado reutilizable. * * Permite mostrar contenido con variantes
visuales (light/solid), distintos * tamaños y colores semánticos, además de iconos opcionales al
inicio y al * final mediante slots. */
<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    /** Variante de estilo del badge (relleno suave o sólido). */
    variant?: 'light' | 'solid'
    /** Tamaño del badge (pequeño o mediano). */
    size?: 'sm' | 'md'
    /** Color semántico del badge (primary, success, error, etc.). */
    color?: 'primary' | 'success' | 'error' | 'warning' | 'info' | 'light' | 'dark'
    /** Clases CSS adicionales para personalizar el badge. */
    className?: string
    /** Icono opcional mostrado antes del contenido (slot `start`). */
    startIcon?: object
    /** Icono opcional mostrado después del contenido (slot `end`). */
    endIcon?: object
  }>(),
  {
    variant: 'light',
    size: 'md',
    color: 'primary',
    className: '',
  },
)

const baseStyles =
  'inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 font-medium capitalize'

const sizeStyles = {
  sm: 'text-theme-xs',
  md: 'text-sm',
}

const variants = {
  light: {
    primary: 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400',
    success: 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
    error: 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    warning: 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
    info: 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
    light: 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80',
    dark: 'bg-gray-500 text-white dark:bg-white/5 dark:text-white',
  },
  solid: {
    primary: 'bg-brand-500 text-white dark:text-white',
    success: 'bg-success-500 text-white dark:text-white',
    error: 'bg-error-500 text-white dark:text-white',
    warning: 'bg-warning-500 text-white dark:text-white',
    info: 'bg-blue-light-500 text-white dark:text-white',
    light: 'bg-gray-400 text-white dark:bg-white/5 dark:text-white/80',
    dark: 'bg-gray-700 text-white dark:text-white',
  },
}

const colorStyles = variants[props.variant][props.color]
</script>
