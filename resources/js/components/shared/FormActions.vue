<template>
  <div :class="['flex items-center gap-3', containerClass]">
    <BaseButton variant="outline" :disabled="loading" @click="emit('cancel')">
      {{ cancelText }}
    </BaseButton>
    <BaseButton variant="primary" :disabled="loading" @click="emit('submit')">
      <span v-if="loading" class="inline-flex items-center gap-2">
        <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
        {{ loadingText }}
      </span>
      <span v-else>{{ submitText }}</span>
    </BaseButton>
  </div>
</template>

<script setup lang="ts">
import BaseButton from '@/components/base/BaseButton.vue'

withDefaults(defineProps<{
  cancelText?: string
  submitText?: string
  loadingText?: string
  loading?: boolean
  containerClass?: string
}>(), {
  cancelText: 'Cancel',
  submitText: 'Save Changes',
  loadingText: 'Saving...',
  loading: false,
  containerClass: '',
})

const emit = defineEmits<{
  (e: 'cancel'): void
  (e: 'submit'): void
}>()
</script>
