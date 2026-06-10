<template>
  <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6"
  >
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Monthly Sales</h3>

      <div class="relative h-fit">
        <DropdownMenu :menu-items="menuItems">
          <template #icon>
            <HorizontalDots class="fill-current" />
          </template>
        </DropdownMenu>
      </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <div id="chartOne" class="-ml-5 min-w-[650px] xl:min-w-full pl-2">
        <VueApexCharts type="bar" height="180" :options="chartOptions" :series="series" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import DropdownMenu from '../shared/DropdownMenu.vue'
import HorizontalDots from '@/icons/HorizontalDots.vue'
import VueApexCharts from 'vue3-apexcharts'
import { createBarChartOptions } from '@/components/charts/chart-options'

const menuItems = [
  { label: 'View More', onClick: () => {} },
  { label: 'Delete', onClick: () => {} },
]

const series = ref([
  {
    name: 'Sales',
    data: [168, 385, 201, 298, 187, 195, 291, 110, 215, 390, 280, 112],
  },
])

const chartOptions = ref(createBarChartOptions())
</script>
