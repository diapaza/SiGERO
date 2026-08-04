<template>
  <ComponentCard title="Objetos en Préstamo">
    <template #header>
      <BaseButton
        v-if="hasPermission('registrar movimientos')"
        size="sm"
        variant="outline"
        @click="router.visit(route('movimientos.index'))"
      >
        Ver todos
      </BaseButton>
    </template>
    <BaseDataTable :columns="columns" :data="objetosPrestados" :page-size="10" />
  </ComponentCard>
</template>

<script setup lang="ts">
import { h } from 'vue'
import { router } from '@inertiajs/vue3'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { usePermissions } from '@/composables/usePermissions'
import { formatDateTime } from '@/utils/date'
import type { Objeto } from '@/types/models'
import type { ColumnDef } from '@tanstack/vue-table'

defineProps<{
  objetosPrestados: Objeto[]
}>()

const { hasPermission } = usePermissions()

const columns: ColumnDef<Objeto>[] = [
  {
    accessorKey: 'objeto',
    header: 'Objeto',
    cell: ({ row }) => {
      const obj = row.original
      return h('div', { class: 'flex items-center gap-3' }, [
        h('div', null, [
          h('p', { class: 'font-medium text-gray-800 dark:text-white/90' }, obj.codigo ?? ''),
          h('p', { class: 'text-xs text-gray-500 dark:text-gray-400' }, obj.nombre ?? ''),
        ]),
      ])
    },
  },
  {
    accessorKey: 'persona',
    header: 'Persona',
    cell: ({ row }) => {
      const user = row.original.movimiento_activo?.user
      return h('span', { class: 'text-gray-600 dark:text-gray-300' }, user?.name ?? '')
    },
  },
  {
    accessorKey: 'telefono',
    header: 'Teléfono',
    cell: ({ row }) => {
      const user = row.original.movimiento_activo?.user
      return h('span', { class: 'text-gray-600 dark:text-gray-300' }, user?.whatsapp_number ?? '-')
    },
  },
  {
    accessorKey: 'fecha_salida',
    header: 'Fecha de Salida',
    cell: ({ row }) => {
      const fecha = row.original.movimiento_activo?.fecha_hora
      return h(
        'span',
        { class: 'text-gray-600 dark:text-gray-300' },
        fecha ? formatDateTime(fecha) : '-',
      )
    },
  },
]
</script>
