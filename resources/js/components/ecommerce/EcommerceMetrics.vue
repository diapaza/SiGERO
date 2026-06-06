<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
    <StatCard
      :icon="UserGroupIcon"
      label="Customers"
      value="3,782"
      :trend="{ direction: 'up', percentage: '11.01%' }"
    />
    <StatCard
      :icon="BoxCubeIcon"
      label="Orders"
      value="5,359"
      :trend="{ direction: 'down', percentage: '9.05%' }"
    />
  </div>
</template>

<script setup lang="ts">
import StatCard from '@/components/shared/StatCard.vue'
import UserGroupIcon from '@/icons/UserGroupIcon.vue'
import BoxCubeIcon from '@/icons/BoxCubeIcon.vue'
</script>
