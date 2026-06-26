<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      id="Vector"
      d="M16 16L12 12M12 12L8 8M12 12L16 8M12 12L8 16"
      stroke="currentColor"
      stroke-width="1.8"
      stroke-linecap="round"
      stroke-linejoin="round"
    ></path>
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
