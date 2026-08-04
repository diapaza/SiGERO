<template>
  <div
    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6"
  >
    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
      <component :is="icon" :class="iconColor" />
    </div>
    <div class="flex items-end justify-between mt-5">
      <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ label }}</span>
        <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
          {{ value }}
        </h4>
      </div>
      <TrendIndicator v-if="trend" v-bind="trend" />
    </div>
  </div>
</template>

/** * Tarjeta de estadística para el panel principal. * * Muestra un icono, una etiqueta, un valor y
un indicador de tendencia opcional. */
<script setup lang="ts">
import TrendIndicator from '@/components/shared/TrendIndicator.vue'

withDefaults(
  defineProps<{
    /** Componente de icono a mostrar en la tarjeta. */
    icon: object
    /** Clases de color del icono. */
    iconColor?: string
    /** Etiqueta descriptiva de la estadística. */
    label: string
    /** Valor numérico o textual de la estadística. */
    value: string | number
    /** Configuración opcional del indicador de tendencia. */
    trend?: {
      /** Dirección de la tendencia: 'up' | 'down'. */
      direction?: 'up' | 'down'
      /** Porcentaje de variación a mostrar. */
      percentage?: string | number
      /** Color explícito del indicador: 'red' | 'green'. */
      color?: 'red' | 'green'
    }
  }>(),
  {
    iconColor: 'fill-gray-800 dark:fill-white/90',
  },
)
</script>
