import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'
import 'vue-sonner/style.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { createApp, h } from 'vue'
import type { DefineComponent } from 'vue'
import pinia from './stores'
import VueApexCharts from 'vue3-apexcharts'
import AppLayout from './App.vue'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob<{ default: DefineComponent }>('./views/**/*.vue', {
      eager: true,
    })
    return pages[`./views/${name}.vue`].default
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({
      render: () => h(AppLayout, null, () => h(App, props)),
    })
    app.use(pinia)
    app.use(VueApexCharts)
    app.use(plugin)
    app.mount(el)
  },
  progress: {
    color: '#4B5563',
    delay: 250,
    includeCSS: true,
    showSpinner: false,
  },
})
