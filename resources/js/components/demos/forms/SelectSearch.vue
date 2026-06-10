<template>
  <div class="space-y-6">
    <!-- Searchable Select -->
    <div>
      <BaseLabel label="Searchable Select" />
      <BaseSelectSearch
        v-model="searchValue"
        :options="frameworkOptions"
        placeholder="Search a framework..."
        clearable
        @change="onChange"
        @focus="onFocus"
        @blur="onBlur"
        @search="onSearch"
        @open="onOpen"
        @close="onClose"
      />
    </div>

    <!-- Searchable + Creatable Select -->
    <div>
      <BaseLabel label="Creatable Select" />
      <BaseSelectSearch
        v-model="creatableValue"
        :options="creatableOptions"
        creatable
        placeholder="Search or create..."
        create-label='Create "{text}"'
        clearable
        @create="onCreate"
        @change="onChange"
        @focus="onFocus"
        @blur="onBlur"
      />
    </div>

    <!-- With Error State -->
    <div>
      <BaseLabel label="With Error" />
      <BaseSelectSearch
        v-model="errorValue"
        :options="frameworkOptions"
        state="error"
        placeholder="Invalid selection..."
      />
    </div>

    <!-- Disabled -->
    <div>
      <BaseLabel label="Disabled" />
      <BaseSelectSearch
        v-model="disabledValue"
        :options="frameworkOptions"
        disabled
        placeholder="Disabled..."
      />
    </div>

    <!-- Loading -->
    <div>
      <BaseLabel label="Loading" />
      <BaseSelectSearch v-model="loadingValue" :options="[]" loading placeholder="Loading..." />
    </div>

    <!-- Event Log -->
    <div v-if="eventLog.length">
      <BaseLabel label="Event Log" />
      <div
        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-3 max-h-32 overflow-y-auto text-xs font-mono text-gray-600 dark:text-gray-400"
      >
        <div v-for="(log, i) in eventLog" :key="i">
          {{ log }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import BaseSelectSearch from '@/components/base/BaseSelectSearch.vue'

const frameworkOptions = [
  { value: 'vue', label: 'Vue.js' },
  { value: 'react', label: 'React' },
  { value: 'angular', label: 'Angular' },
  { value: 'svelte', label: 'Svelte' },
  { value: 'solid', label: 'SolidJS' },
  { value: 'qwik', label: 'Qwik' },
  { value: 'nuxt', label: 'Nuxt' },
  { value: 'next', label: 'Next.js' },
  { value: 'remix', label: 'Remix' },
]

const creatableOptions = ref([
  { value: 'design', label: 'Design' },
  { value: 'development', label: 'Development' },
  { value: 'marketing', label: 'Marketing' },
  { value: 'sales', label: 'Sales' },
])

const searchValue = ref<string | number | null>(null)
const creatableValue = ref<string | number | null>(null)
const errorValue = ref<string | number | null>(null)
const disabledValue = ref<string | number | null>('vue')
const loadingValue = ref<string | number | null>(null)

const eventLog = ref<string[]>([])
const maxLogs = 15

function log(msg: string) {
  eventLog.value.unshift(`[${new Date().toLocaleTimeString()}] ${msg}`)
  if (eventLog.value.length > maxLogs) {
    eventLog.value = eventLog.value.slice(0, maxLogs)
  }
}

function onChange(value: string | number | null) {
  log(`change: ${value}`)
}

function onFocus(e: FocusEvent) {
  log('focus')
}

function onBlur(e: FocusEvent) {
  log('blur')
}

function onSearch(term: string) {
  log(`search: "${term}"`)
}

function onOpen() {
  log('open')
}

function onClose() {
  log('close')
}

function onCreate(value: string) {
  log(`create: "${value}"`)
  const exists = creatableOptions.value.some(
    (opt) => opt.label.toLowerCase() === value.toLowerCase(),
  )
  if (!exists) {
    const newOption = { value: value.toLowerCase().replace(/\s+/g, '-'), label: value }
    creatableOptions.value.push(newOption)
    creatableValue.value = newOption.value
  }
}
</script>
