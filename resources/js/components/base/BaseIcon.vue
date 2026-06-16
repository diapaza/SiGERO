<template>
  <component
    :is="icon"
    :size="size"
    :class="['inline-flex items-center justify-center', className]"
  />
</template>

<script setup lang="ts">
defineProps<{
  icon: object
  className?: string
  size?: number
}>()
</script>
