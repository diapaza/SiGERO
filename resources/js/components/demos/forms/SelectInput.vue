<template>
  <div class="space-y-6">
    <!-- Single Select Input -->
    <div>
      <BaseLabel label="Select Input" />
      <BaseSelect
        v-model="singleSelect"
        placeholder="Select Option"
        :options="selectOptions"
      />
    </div>

    <!-- Multiple Select Input -->
    <div>
      <MultipleSelect v-model="selectedItems" :options="optionss" class="w-full" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import BaseSelect from '@/components/base/BaseSelect.vue'
import MultipleSelect from './MultipleSelect.vue'

const optionss = [
  { value: 'apple', label: 'Apple' },
  { value: 'banana', label: 'Banana' },
  { value: 'cherry', label: 'Cherry' },
  { value: 'date', label: 'Date' },
  { value: 'elderberry', label: 'Elderberry' },
  { value: 'graphs', label: 'Graphs' },
]

const selectedItems = ref([])

const singleSelect = ref('')

const selectOptions = [
  { value: 'marketing', label: 'Marketing' },
  { value: 'template', label: 'Template' },
  { value: 'development', label: 'Development' },
]

const options = ref([
  { text: 'Option 1', selected: false },
  { text: 'Option 2', selected: false },
  { text: 'Option 3', selected: false },
  { text: 'Option 4', selected: false },
])

const selected = computed(() => options.value.filter((option) => option.selected))
</script>
