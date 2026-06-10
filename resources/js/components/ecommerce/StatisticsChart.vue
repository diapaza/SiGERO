<template>
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6"
  >
    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between">
      <div class="w-full">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Statistics</h3>
        <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
          Target you’ve set for each month
        </p>
      </div>

      <div class="flex items-center gap-3">
        <div class="relative">
          <div
            class="inline-flex items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900"
          >
            <button
              v-for="option in options"
              :key="option.value"
              :class="[
                selected === option.value
                  ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800'
                  : 'text-gray-500 dark:text-gray-400',
                'px-3 py-2 font-medium rounded-md text-theme-sm hover:text-gray-900 hover:shadow-theme-xs dark:hover:bg-gray-800 dark:hover:text-white',
              ]"
              @click="selected = option.value"
            >
              {{ option.label }}
            </button>
          </div>
        </div>

        <div class="relative">
          <flat-pickr
            v-model="date"
            :config="flatpickrConfig"
            class="pl-3 sm:pl-9 dark:bg-dark-900 h-10 w-10 sm:w-40 rounded-lg border border-gray-200 bg-white text-transparent sm:text-theme-sm sm:text-gray-800 shadow-theme-xs placeholder:text-transparent sm:placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-transparent sm:dark:text-gray-400 dark:placeholder:text-transparent sm:dark:placeholder:text-gray-400 dark:focus:border-brand-800"
            placeholder="Select Date"
          />
          <span
            class="absolute text-gray-500 -translate-y-1/2 pointer-events-none left-1/2 -translate-x-1/2 sm:left-3 sm:translate-x-0 top-1/2 dark:text-gray-400"
          >
            <CalenderIcon class="fill-current" />
          </span>
        </div>
      </div>
    </div>
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <div id="chartThree" class="-ml-4 min-w-[1000px] xl:min-w-full pl-2">
        <VueApexCharts type="area" height="310" :options="chartOptions" :series="series" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import flatPickr from 'vue-flatpickr-component'
import CalenderIcon from '@/icons/CalenderIcon.vue'
import VueApexCharts from 'vue3-apexcharts'
import { createAreaChartOptions } from '@/components/charts/chart-options'

const options = [
  { value: 'optionOne', label: 'Monthly' },
  { value: 'optionTwo', label: 'Quarterly' },
  { value: 'optionThree', label: 'Annually' },
]

const selected = ref('optionOne')
const date = ref('')

const flatpickrConfig = {
  mode: 'range',
  dateFormat: 'M j',
  defaultDate: [new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), new Date()],
}

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

<style scoped>
.area-chart {
  width: 100%;
}
</style>
