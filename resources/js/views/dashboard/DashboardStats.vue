<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
    <StatCard
      :icon="BoxCubeIcon"
      icon-color="text-blue-500"
      label="Total Objetos"
      :value="stats.total"
    />
    <StatCard
      :icon="CheckIcon"
      icon-color="text-green-500"
      label="Disponibles"
      :value="stats.disponibles"
    />
    <StatCard
      :icon="WarningIcon"
      icon-color="text-orange-500"
      label="Prestados"
      :value="stats.prestados"
    />
    <StatCard
      :icon="UserGroupIcon"
      icon-color="text-purple-500"
      label="Usuarios"
      :value="usuariosTotal"
    />
  </div>
</template>

/** * Tarjetas de estadísticas del dashboard. * * Subcomponente de `Dashboard.vue`. Muestra total de
objetos, disponibles, * prestados y usuarios. * * Props Inertia: `stats` (Estadisticas),
`usuariosTotal`. */
<script setup lang="ts">
import StatCard from '@/components/shared/StatCard.vue'
import BoxCubeIcon from '@/icons/BoxCubeIcon.vue'
import CheckIcon from '@/icons/CheckIcon.vue'
import WarningIcon from '@/icons/WarningIcon.vue'
import UserGroupIcon from '@/icons/UserGroupIcon.vue'
import type { Estadisticas } from '@/types/models'

defineProps<{
  stats: Estadisticas
  usuariosTotal: number
}>()
</script>
