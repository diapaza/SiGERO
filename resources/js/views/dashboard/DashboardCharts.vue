<template>
  <div class="grid grid-cols-1 gap-4 md:gap-6 xl:grid-cols-3">
    <div class="xl:col-span-2">
      <ComponentCard title="Movimientos por Mes">
        <BaseChart type="bar" height="350" :options="barChartOptions" :series="barChartSeries" />
      </ComponentCard>
    </div>
    <div>
      <ComponentCard title="Objetos por Categoría">
        <BaseChart
          chart-id="pie-chart"
          type="pie"
          height="350"
          :options="donutChartOptions"
          :series="donutChartSeries"
        />
      </ComponentCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseChart from '@/components/base/BaseChart.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import type { MovimientosPorMes, ObjetosPorCategoria } from '@/types/models'
import type { ApexOptions } from 'apexcharts'

const props = defineProps<{
  movimientosPorMes: MovimientosPorMes[]
  objetosPorCategoria: ObjetosPorCategoria[]
}>()

const monthNames = [
  'Ene',
  'Feb',
  'Mar',
  'Abr',
  'May',
  'Jun',
  'Jul',
  'Ago',
  'Sep',
  'Oct',
  'Nov',
  'Dic',
]

const barChartSeries = computed(() => {
  const salidas = Array(12).fill(0)
  const retornos = Array(12).fill(0)

  props.movimientosPorMes.forEach((item) => {
    const monthIndex = parseInt(item.mes, 10) - 1
    if (item.tipo_movimiento === 'salida') {
      salidas[monthIndex] = item.total
    } else {
      retornos[monthIndex] = item.total
    }
  })

  return [
    { name: 'Salidas', data: salidas },
    { name: 'Retornos', data: retornos },
  ]
})

const barChartOptions = computed<ApexOptions>(() => ({
  colors: ['#465fff', '#12b76a'],
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'bar',
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '39%',
      borderRadius: 5,
      borderRadiusApplication: 'end',
    },
  },
  dataLabels: { enabled: false },
  stroke: {
    show: true,
    width: 4,
    colors: ['transparent'],
  },
  xaxis: {
    categories: monthNames,
    axisBorder: { show: false },
    axisTicks: { show: false },
  },
  legend: {
    show: true,
    position: 'top',
    horizontalAlign: 'left',
    fontFamily: 'Outfit',
    markers: { radius: 99 },
  },
  yaxis: { title: false },
  grid: {
    yaxis: { lines: { show: true } },
  },
  fill: { opacity: 1 },
  tooltip: {
    x: { show: false },
    y: {
      formatter: (val: number) => val.toString(),
    },
  },
}))

const donutChartSeries = computed(() => props.objetosPorCategoria.map((cat) => cat.total))

const donutChartOptions = computed<ApexOptions>(() => ({
  colors: ['#465fff', '#12b76a', '#f79009', '#f04438', '#0ba5ec', '#fb6514'],
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'pie',
  },
  stroke: {
    show: false,
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(0)}%`,
    style: {
      fontSize: '14px',
      fontWeight: 600,
      fontFamily: 'Outfit, sans-serif',
      colors: ['#ffffff'],
    },
    dropShadow: {
      enabled: true,
      top: 1,
      left: 1,
      blur: 2,
      color: 'rgba(0,0,0,0.45)',
      opacity: 0.6,
    },
  },
  legend: {
    show: true,
    position: 'bottom',
    fontFamily: 'Outfit',
    markers: { radius: 99 },
  },
  labels: props.objetosPorCategoria.map((cat) => cat.nombre),
  tooltip: {
    custom: ({ series, seriesIndex, w }) => {
      const color = w.globals.colors[seriesIndex]
      const label = w.globals.labels?.[seriesIndex] ?? ''
      const total = series.reduce((a: number, b: number) => a + b, 0)
      const percentage = ((series[seriesIndex] / total) * 100).toFixed(1)
      return `<div style="background:#fff;padding:12px 16px;border-radius:8px;border:1px solid #e4e7ec;box-shadow:0 4px 16px rgba(0,0,0,.08);font-family:Outfit,sans-serif;">
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="width:10px;height:10px;border-radius:50%;background:${color};display:inline-block;"></span>
          <span style="color:#344054;font-weight:500;font-size:14px;">${label}</span>
        </div>
        <div style="color:#667085;font-size:13px;margin-top:4px;margin-left:18px;">
          ${percentage}% (${series[seriesIndex]} objetos)
        </div>
      </div>`
    },
  },
}))
</script>
