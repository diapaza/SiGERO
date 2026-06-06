<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div className="space-y-5 sm:space-y-6">
      <ComponentCard title="Responsive Image">
        <ResponsiveImage />
      </ComponentCard>
      <ComponentCard title="Image in 2 Grid">
        <TwoColumnImageGrid />
      </ComponentCard>
      <ComponentCard title="Image in 3 Grid">
        <ThreeColumnImageGrid />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import ResponsiveImage from '@/components/demos/images/ResponsiveImage.vue'
import TwoColumnImageGrid from '@/components/demos/images/TwoColumnImageGrid.vue'
import ThreeColumnImageGrid from '@/components/demos/images/ThreeColumnImageGrid.vue'
const currentPageTitle = ref('Images')
</script>

<style></style>
