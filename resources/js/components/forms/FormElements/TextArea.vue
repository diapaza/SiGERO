<template>
  <div class="space-y-6">
    <!-- Normal Textarea -->
    <div>
      <BaseLabel label="Description" />
      <BaseTextarea
        v-model="normalDescription"
        placeholder="Enter a description..."
        rows="6"
      />
    </div>

    <!-- Disabled Textarea -->
    <div>
      <BaseLabel label="Description" disabled />
      <BaseTextarea
        v-model="disabledDescription"
        placeholder="Enter a description..."
        rows="6"
        disabled
      />
    </div>

    <!-- Error State Textarea -->
    <div>
      <BaseLabel label="Description" />
      <BaseTextarea
        v-model="errorDescription"
        placeholder="Enter a description..."
        rows="6"
        state="error"
      />
      <p class="mt-1.5 text-theme-xs text-error-500">Please enter a message in the textarea.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import BaseTextarea from '@/components/base/BaseTextarea.vue'

const normalDescription = ref('')
const disabledDescription = ref('This textarea is disabled')
const errorDescription = ref('')
</script>
