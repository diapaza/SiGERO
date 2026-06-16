<template>
  <BaseCard :title="title" :description="desc" :card-class="className" content-class="space-y-5">
    <template #header>
      <slot name="header" />
    </template>
    <slot />
  </BaseCard>
</template>

<script setup lang="ts">
import BaseCard from '@/components/base/BaseCard.vue'

interface Props {
  title: string
  className?: string
  desc?: string
}

const props = defineProps<Props>()
</script>
