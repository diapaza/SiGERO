import type { ApexOptions } from 'apexcharts'

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

interface BarChartOptions {
  colors?: string[]
  showToolbar?: boolean
  showLegend?: boolean
  columnWidth?: string
  borderRadius?: number
}

export function createBarChartOptions(options: BarChartOptions = {}): ApexOptions {
  const {
    colors = ['#465fff'],
    showToolbar = false,
    showLegend = true,
    columnWidth = '39%',
    borderRadius = 5,
  } = options

  return {
    colors,
    chart: {
      fontFamily: 'Outfit, sans-serif',
      type: 'bar',
      toolbar: { show: showToolbar },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth,
        borderRadius,
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
      categories: months,
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    legend: {
      show: showLegend,
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
  }
}

interface AreaChartOptions {
  colors?: string[]
  showToolbar?: boolean
  showLegend?: boolean
  showGradient?: boolean
}

export function createAreaChartOptions(options: AreaChartOptions = {}): ApexOptions {
  const {
    colors = ['#465FFF', '#9CB9FF'],
    showToolbar = false,
    showLegend = false,
    showGradient = true,
  } = options

  return {
    legend: {
      show: showLegend,
      position: 'top',
      horizontalAlign: 'left',
    },
    colors,
    chart: {
      fontFamily: 'Outfit, sans-serif',
      type: 'area',
      toolbar: { show: showToolbar },
    },
    fill: {
      gradient: {
        enabled: showGradient,
        opacityFrom: 0.55,
        opacityTo: 0,
      },
    },
    stroke: {
      curve: 'straight',
      width: [2, 2],
    },
    markers: { size: 0 },
    labels: { show: false, position: 'top' },
    grid: {
      xaxis: { lines: { show: false } },
      yaxis: { lines: { show: true } },
    },
    dataLabels: { enabled: false },
    tooltip: {
      x: { format: 'dd MMM yyyy' },
    },
    xaxis: {
      type: 'category',
      categories: months,
      axisBorder: { show: false },
      axisTicks: { show: false },
      tooltip: { enabled: false },
    },
    yaxis: {
      title: { style: { fontSize: '0px' } },
    },
  }
}
