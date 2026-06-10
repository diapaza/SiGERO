<template>
  <div
    v-if="error"
    :class="[
      'rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-800 dark:bg-error-900/20',
      wrapperClass,
    ]"
  >
    <div class="flex items-start gap-3">
      <div class="shrink-0">
        <svg class="w-5 h-5 text-error-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
          />
        </svg>
      </div>
      <div class="flex-1">
        <h4 class="mb-1 text-sm font-semibold text-error-800 dark:text-error-200">
          {{ title }}
        </h4>
        <p class="text-sm text-error-600 dark:text-error-300">
          {{ error.message || 'An unexpected error occurred.' }}
        </p>
      </div>
      <button
        v-if="retryable"
        class="shrink-0 rounded-md bg-error-100 px-3 py-1.5 text-xs font-medium text-error-700 hover:bg-error-200 dark:bg-error-800 dark:text-error-200 dark:hover:bg-error-700"
        @click="retry"
      >
        Retry
      </button>
    </div>
  </div>
  <slot v-else />
</template>

<script setup lang="ts">
interface Props {
  title?: string
  retryable?: boolean
  wrapperClass?: string
}

withDefaults(defineProps<Props>(), {
  title: 'Something went wrong',
  retryable: false,
  wrapperClass: '',
})

const emit = defineEmits<{
  (e: 'retry'): void
}>()

const error = defineModel<Error | null>('error', { default: null })

const retry = () => {
  error.value = null
  emit('retry')
}
</script>
