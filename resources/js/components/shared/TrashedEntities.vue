<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard :title="cardTitle" :desc="cardDesc">
        <template #header>
          <BaseButton variant="outline" size="sm" :start-icon="ChevronLeftIcon" @click="goBack">
            {{ backLabel }}
          </BaseButton>
        </template>

        <div class="mb-4">
          <BaseInput v-model="search" :placeholder="searchPlaceholder" class-name="max-w-sm" />
        </div>

        <BaseDataTable
          :columns="columns"
          :data="filteredEntities"
          :global-filter="search"
          :page-size="10"
        />

        <div
          v-if="filteredEntities.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          {{ emptyMessage }}
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

/** * Página genérica para listar y restaurar entidades eliminadas (soft deletes). * * Muestra una
tabla con los registros en papelera, un buscador sobre los campos * indicados y un botón para volver
al índice de la entidad. */
<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import type { ColumnDef } from '@tanstack/vue-table'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import { ChevronLeftIcon } from '@/icons'

interface Props {
  /** Registros eliminados (soft deletes) de la entidad. */
  entities: any[]
  /** Prefijo de las rutas de la entidad (ej. 'objetos'). */
  routePrefix: string
  /** Nombre en singular de la entidad (ej. 'Objeto'). */
  entityLabel: string
  /** Título de la página. */
  pageTitle: string
  /** Título de la tarjeta. */
  cardTitle: string
  /** Descripción de la tarjeta. */
  cardDesc: string
  /** Texto del botón de retorno. */
  backLabel: string
  /** Placeholder del campo de búsqueda. */
  searchPlaceholder: string
  /** Mensaje mostrado cuando no hay registros. */
  emptyMessage: string
  /** Definición de columnas de la tabla. */
  columns: ColumnDef<any>[]
  /** Campos de la entidad sobre los que se realiza la búsqueda. */
  searchFields: string[]
}

const props = defineProps<Props>()

// Término de búsqueda sobre los registros de la papelera.
const search = ref('')

/** Entidades filtradas según el término de búsqueda en los campos indicados. */
const filteredEntities = computed(() => {
  if (!search.value) return props.entities
  const term = search.value.toLowerCase()
  return props.entities.filter((entity) =>
    props.searchFields.some((field) => {
      const value = entity[field]
      return value && String(value).toLowerCase().includes(term)
    }),
  )
})

/** Navega de vuelta al índice de la entidad. */
const goBack = () => {
  router.get(route(`${props.routePrefix}.index`))
}
</script>
