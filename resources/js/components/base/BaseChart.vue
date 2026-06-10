<template>
  <div class="max-w-full overflow-x-auto custom-scrollbar">
    <div :id="chartId" :class="['pl-2', containerClass]">
      <VueApexCharts :type="type" :height="height" :options="options" :series="series" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'

interface Props {
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
  height?: number | string
  options: ApexOptions
  series: Array<{ name: string; data: number[] }>
  chartId?: string
  containerClass?: string
}

const props = withDefaults(defineProps<Props>(), {
  height: 350,
  chartId: () => `chart-${Math.random().toString(36).slice(2, 9)}`,
  containerClass: '',
})
</script>
