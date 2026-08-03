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
  entities: any[]
  routePrefix: string
  entityLabel: string
  pageTitle: string
  cardTitle: string
  cardDesc: string
  backLabel: string
  searchPlaceholder: string
  emptyMessage: string
  columns: ColumnDef<any>[]
  searchFields: string[]
}

const props = defineProps<Props>()

const search = ref('')

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

const goBack = () => {
  router.get(route(`${props.routePrefix}.index`))
}
</script>
