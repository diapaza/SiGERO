<template>
  <span
    :class="[
      'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-sm font-medium',
      colorClasses,
    ]"
  >
    <TrendArrowIcon :direction="direction" :color="computedColor" :size="12" />
    {{ percentage }}
  </span>
</template>

/** * Indicador de tendencia para tarjetas de estadística. * * Muestra un porcentaje con una flecha
de dirección (arriba/abajo) coloreada * según la tendencia (verde/rojo). */
<script setup lang="ts">
import { computed } from 'vue'
import TrendArrowIcon from '@/icons/TrendArrowIcon.vue'

const props = withDefaults(
  defineProps<{
    /** Dirección de la tendencia: 'up' | 'down'. */
    direction?: 'up' | 'down'
    /** Porcentaje de variación a mostrar. */
    percentage?: string | number
    /** Color explícito del indicador: 'red' | 'green'. */
    color?: 'red' | 'green'
  }>(),
  {
    direction: 'up',
    percentage: '',
  },
)

/** Color final del indicador (explícito o derivado de la dirección). */
const computedColor = computed(() => {
  if (props.color) return props.color
  return props.direction === 'up' ? 'green' : 'red'
})

/** Clases de estilo del indicador según su color. */
const colorClasses = computed(() => {
  const isGreen = computedColor.value === 'green'
  return isGreen
    ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
    : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'
})
</script>
