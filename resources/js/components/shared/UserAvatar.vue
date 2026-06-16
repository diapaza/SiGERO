<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 48 48"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <defs>
      <linearGradient :id="gradientId" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" :stop-color="gradient.from" />
        <stop offset="100%" :stop-color="gradient.to" />
      </linearGradient>
    </defs>
    <circle cx="24" cy="24" r="24" :fill="`url(#${gradientId})`" />
    <text
      x="24"
      y="24"
      text-anchor="middle"
      dominant-baseline="central"
      fill="white"
      font-size="18"
      font-weight="700"
      font-family="system-ui, -apple-system, sans-serif"
    >
      {{ initials }}
    </text>
  </svg>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { getInitials, getGradientColors } from '@/composables/useUserAvatar'

const props = withDefaults(
  defineProps<{
    name: string
    size?: number
  }>(),
  {
    size: 44,
  },
)

const initials = computed(() => getInitials(props.name))
const gradient = computed(() => getGradientColors(props.name))

const gradientId = `ag-${Math.random().toString(36).substring(2, 10)}`
</script>
