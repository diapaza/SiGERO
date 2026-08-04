<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="currentPageTitle" />
    <div class="space-y-6">
      <DashboardStats :stats="estadisticas" :usuarios-total="usuariosTotal" />

      <template v-if="hasReportPermission">
        <DashboardCharts
          :movimientos-por-mes="movimientosPorMes"
          :objetos-por-categoria="objetosPorCategoria"
        />
      </template>

      <DashboardSalidas :objetos-prestados="objetosPrestados" />
    </div>
  </AdminLayout>
</template>

/** * Página principal (Dashboard). * * Vista renderizada por `DashboardController@index`. Muestra
las tarjetas de * estadísticas, los gráficos (solo con permiso `ver reportes`) y la tabla de *
objetos en préstamo. Se refresca por polling cada 30 segundos con `usePoll`, * actualizando
únicamente sus props. * * Props Inertia: `estadisticas`, `usuariosTotal`, `movimientosPorMes`, *
`objetosPorCategoria`, `objetosPrestados`. */
<script setup lang="ts">
import { computed } from 'vue'
import { usePage, usePoll } from '@inertiajs/vue3'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import DashboardStats from '@/views/dashboard/DashboardStats.vue'
import DashboardCharts from '@/views/dashboard/DashboardCharts.vue'
import DashboardSalidas from '@/views/dashboard/DashboardSalidas.vue'
import type { Estadisticas, MovimientosPorMes, ObjetosPorCategoria, Objeto } from '@/types/models'

defineProps<{
  estadisticas: Estadisticas
  usuariosTotal: number
  movimientosPorMes: MovimientosPorMes[]
  objetosPorCategoria: ObjetosPorCategoria[]
  objetosPrestados: Objeto[]
}>()

const currentPageTitle = 'Dashboard'

const userPermissions = computed(() => (usePage().props.auth as any)?.user?.permissions ?? [])
const hasReportPermission = computed(() => userPermissions.value.includes('ver reportes'))

usePoll(30000, {
  only: [
    'estadisticas',
    'usuariosTotal',
    'movimientosPorMes',
    'objetosPorCategoria',
    'objetosPrestados',
  ],
})
</script>
