<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Primary Button">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="primary"> Button Text </BaseButton>
          <BaseButton size="md" variant="primary"> Button Text </BaseButton>
        </div>
      </ComponentCard>
      <ComponentCard title="Primary Button with Left Icon">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="primary" :startIcon="BoxIcon"> Button Text </BaseButton>
          <BaseButton size="md" variant="primary" :startIcon="BoxIcon"> Button Text </BaseButton>
        </div>
      </ComponentCard>
      <ComponentCard title="Primary Button with Right Icon">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="primary" :endIcon="BoxIcon"> Button Text </BaseButton>
          <BaseButton size="md" variant="primary" :endIcon="BoxIcon"> Button Text </BaseButton>
        </div>
      </ComponentCard>
      <ComponentCard title="Primary Button">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="outline"> Button Text </BaseButton>
          <BaseButton size="md" variant="outline"> Button Text </BaseButton>
        </div>
      </ComponentCard>
      <ComponentCard title="Primary Button with Left Icon">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="outline" :startIcon="BoxIcon"> Button Text </BaseButton>
          <BaseButton size="md" variant="outline" :startIcon="BoxIcon"> Button Text </BaseButton>
        </div>
      </ComponentCard>
      <ComponentCard title="Primary Button with Right Icon">
        <div class="flex items-center gap-5">
          <BaseButton size="sm" variant="outline" :endIcon="BoxIcon"> Button Text </BaseButton>
          <BaseButton size="md" variant="outline" :endIcon="BoxIcon"> Button Text </BaseButton>
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import { BoxIcon } from '@/icons'
const currentPageTitle = ref('Buttons')
</script>

<style></style>
