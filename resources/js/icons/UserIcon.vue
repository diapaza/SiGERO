<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      d="M12 2a4 4 0 100 8 4 4 0 000-8zm8 15.5c0 2.485 0 4.5-8 4.5s-8-2.015-8-4.5C4 15.015 7.582 13 12 13s8 2.015 8 4.5z"
      fill="currentColor"
    />
  </svg>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    size?: number
  }>(),
  {
    size: 24,
  },
)
</script>
