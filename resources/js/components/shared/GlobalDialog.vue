<template>
  <BaseModal v-model:is-open="state.isOpen" size="sm" @close="handleClose">
    <template #body>
      <div class="flex flex-col items-center text-center">
        <div
          v-if="iconComponent"
          class="flex size-14 items-center justify-center rounded-full"
          :class="iconBgClass"
        >
          <component :is="iconComponent" class="size-7" :class="iconColorClass" />
        </div>

        <h3 v-if="state.title" class="mt-4 text-lg font-semibold text-gray-800 dark:text-white/90">
          {{ state.title }}
        </h3>

        <p v-if="state.description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          {{ state.description }}
        </p>

        <div v-if="state.type === 'prompt'" class="mt-5 w-full">
          <input
            ref="inputRef"
            v-model="inputValue"
            type="text"
            :placeholder="state.placeholder"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            @keydown.enter.prevent="handleConfirm"
          />
        </div>
      </div>

      <div class="mt-6 flex items-center justify-center gap-3">
        <button
          v-if="state.type !== 'alert'"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
          @click="handleCancel"
        >
          {{ state.cancelLabel }}
        </button>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white shadow-theme-xs transition"
          :class="confirmBtnClass"
          @click="handleConfirm"
        >
          {{ state.confirmLabel }}
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'
import { useDialog } from '@/composables/useDialog'
import { WarningIcon, SuccessIcon, ErrorIcon, InfoCircleIcon, QuestionIcon } from '@/icons'
import type { Component } from 'vue'

const { state, close } = useDialog()

const inputValue = ref('')
const inputRef = ref<HTMLInputElement | null>(null)

const iconMap: Record<string, Component> = {
  warning: WarningIcon,
  success: SuccessIcon,
  error: ErrorIcon,
  info: InfoCircleIcon,
  question: QuestionIcon,
}

const iconBgMap: Record<string, string> = {
  warning: 'bg-warning-50 dark:bg-warning-500/15',
  error: 'bg-error-50 dark:bg-error-500/15',
  success: 'bg-success-50 dark:bg-success-500/15',
  info: 'bg-blue-light-50 dark:bg-blue-light-500/15',
  question: 'bg-brand-50 dark:bg-brand-500/15',
}

const iconColorMap: Record<string, string> = {
  warning: 'text-warning-500',
  error: 'text-error-500',
  success: 'text-success-500',
  info: 'text-blue-light-500',
  question: 'text-brand-500',
}

const iconComponent = computed(() => (state.icon ? iconMap[state.icon] : null))
const iconBgClass = computed(() => (state.icon ? iconBgMap[state.icon] : ''))
const iconColorClass = computed(() => (state.icon ? iconColorMap[state.icon] : ''))

const confirmBtnClass = computed(() =>
  state.destructive
    ? 'bg-error-500 hover:bg-error-600 disabled:bg-error-300'
    : 'bg-brand-500 hover:bg-brand-600 disabled:bg-brand-300',
)

watch(
  () => state.isOpen,
  (open) => {
    if (open) {
      inputValue.value = state.initialValue
      nextTick(() => inputRef.value?.focus())
    }
  },
)

function handleConfirm() {
  if (state.type === 'prompt') {
    close(inputValue.value)
  } else {
    close(true)
  }
}

function handleCancel() {
  if (state.type === 'prompt') {
    close(null)
  } else {
    close(false)
  }
}

function handleClose() {
  const value = state.type === 'prompt' ? null : false
  close(value)
}
</script>
