<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="currentPageTitle" />
    <div class="space-y-6">
      <DashboardStats :stats="estadisticas" :usuarios-total="usuariosTotal" />

      <template v-if="isAdminOrPersonal">
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
import { usePage } from '@inertiajs/vue3'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import DashboardStats from '@/views/dashboard/DashboardStats.vue'
import DashboardCharts from '@/views/dashboard/DashboardCharts.vue'
import DashboardSalidas from '@/views/dashboard/DashboardSalidas.vue'
import type { Estadisticas, MovimientosPorMes, ObjetosPorCategoria, Objeto } from '@/types/models'

const props = defineProps<{
  estadisticas: Estadisticas
  usuariosTotal: number
  movimientosPorMes: MovimientosPorMes[]
  objetosPorCategoria: ObjetosPorCategoria[]
  objetosPrestados: Objeto[]
}>()

const currentPageTitle = 'Dashboard'

const user = computed(() => (usePage().props.auth as any).user)
const isAdminOrPersonal = computed(() =>
  ['Administrador', 'Personal'].includes(user.value?.role?.nombre),
)
</script>
