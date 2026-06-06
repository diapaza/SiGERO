<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center gap-8">
      <!-- Default Checkbox -->
      <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer select-none dark:text-gray-400">
        <BaseCheckbox v-model="checkboxOne" />
        <span class="ml-2">Default</span>
      </label>

      <!-- Checked Checkbox -->
      <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer select-none dark:text-gray-400">
        <BaseCheckbox v-model="checkboxTwo" />
        <span class="ml-2">Checked</span>
      </label>

      <!-- Disabled Checkbox -->
      <label class="flex items-center text-sm font-medium text-gray-300 cursor-pointer select-none dark:text-gray-700">
        <BaseCheckbox v-model="checkboxThree" disabled />
        <span class="ml-2">Disabled</span>
      </label>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseCheckbox from '@/components/base/BaseCheckbox.vue'

const checkboxOne = ref(false)
const checkboxTwo = ref(true)
const checkboxThree = ref(true)
</script>
