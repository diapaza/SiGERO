<template>
  <div class="max-w-full overflow-x-auto custom-scrollbar">
    <div id="chartThree" class="-ml-4 min-w-[1000px] xl:min-w-full pl-2">
      <VueApexCharts type="area" height="310" :options="chartOptions" :series="series" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import { createAreaChartOptions } from '../chart-options'

const series = ref([
  {
    name: 'Sales',
    data: [180, 190, 170, 160, 175, 165, 170, 205, 230, 210, 240, 235],
  },
  {
    name: 'Revenue',
    data: [40, 30, 50, 40, 55, 40, 70, 100, 110, 120, 150, 140],
  },
])

const chartOptions = ref(createAreaChartOptions())
</script>
