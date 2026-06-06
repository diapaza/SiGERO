<template>
  <BaseCard :title="title" :description="desc" :cardClass="className" contentClass="space-y-5">
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
