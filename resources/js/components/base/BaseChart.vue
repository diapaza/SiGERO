<template>
  <div class="max-w-full overflow-x-auto custom-scrollbar">
    <div :id="chartId" :class="['pl-2', containerClass]">
      <VueApexCharts :type="type" :height="height" :options="options" :series="series" />
    </div>
  </div>
</template>

/** * Envoltorio reutilizable para gráficos ApexCharts. * * Renderiza un gráfico con scroll
horizontal en pantallas pequeñas y recibe * las opciones y series directamente del consumidor. */
<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'

interface Props {
  /** Tipo de gráfico a renderizar (bar, area, line, pie, etc.). */
  type:
    | 'bar'
    | 'area'
    | 'line'
    | 'pie'
    | 'donut'
    | 'radialBar'
    | 'scatter'
    | 'bubble'
    | 'heatmap'
    | 'candlestick'
  /** Altura del gráfico en píxeles o porcentaje. */
  height?: number | string
  /** Opciones de configuración del gráfico de ApexCharts. */
  options: ApexOptions
  /** Series de datos a representar en el gráfico. */
  series: ApexOptions['series']
  /** Identificador único del contenedor del gráfico. */
  chartId?: string
  /** Clases CSS adicionales aplicadas al contenedor del gráfico. */
  containerClass?: string
}

withDefaults(defineProps<Props>(), {
  height: 350,
  chartId: () => `chart-${Math.random().toString(36).slice(2, 9)}`,
  containerClass: '',
})
</script>
