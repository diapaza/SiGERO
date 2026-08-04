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
