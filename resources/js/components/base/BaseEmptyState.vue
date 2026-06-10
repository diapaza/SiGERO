<template>
  <div :class="['flex flex-col items-center justify-center py-12 px-6 text-center', wrapperClass]">
    <div v-if="$slots.icon" class="mb-4 text-gray-400 dark:text-gray-500">
      <slot name="icon" />
    </div>
    <div v-else class="mb-4">
      <svg
        class="w-12 h-12 text-gray-300 dark:text-gray-600"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
        />
      </svg>
    </div>
    <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
      <slot name="title">
        {{ title }}
      </slot>
    </h3>
    <p
      v-if="description || $slots.description"
      class="mb-6 max-w-sm text-sm text-gray-500 dark:text-gray-400"
    >
      <slot name="description">
        {{ description }}
      </slot>
    </p>
    <slot name="action" />
  </div>
</template>

<script setup lang="ts">
interface Props {
  title?: string
  description?: string
  wrapperClass?: string
}

withDefaults(defineProps<Props>(), {
  title: 'No data available',
  description: '',
  wrapperClass: '',
})
</script>
