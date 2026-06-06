<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Basic Data Table">
        <DataTableBasic />
      </ComponentCard>

      <ComponentCard title="Data Table with Filters">
        <DataTableFilter />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import DataTableBasic from '@/components/tables/data-tables/DataTableBasic.vue'
import DataTableFilter from '@/components/tables/data-tables/DataTableFilter.vue'

const currentPageTitle = ref('Data Tables')
</script>
