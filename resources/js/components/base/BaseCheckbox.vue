<template>
  <label
    :for="id"
    :class="[
      'inline-flex items-center gap-3 text-sm font-medium cursor-pointer select-none',
      labelClass,
    ]"
  >
    <span class="relative">
      <input
        :id="id"
        :name="name"
        type="checkbox"
        class="sr-only"
        :checked="modelValue"
        :disabled="disabled"
        @change="onChange"
      />
      <span
        :class="[
          'mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px] transition-all',
          modelValue
            ? 'border-brand-500 bg-brand-500'
            : 'border-gray-300 bg-transparent dark:border-gray-700',
          disabled ? 'opacity-50 cursor-not-allowed' : '',
        ]"
      >
        <CheckIcon v-if="modelValue" class="text-white" />
      </span>
    </span>
    <span
      ><slot>{{ label }}</slot></span
    >
  </label>
</template>

<script setup lang="ts">
import CheckIcon from '@/icons/CheckIcon.vue'

const props = withDefaults(
  defineProps<{
    modelValue?: boolean
    label?: string
    disabled?: boolean
    id?: string
    name?: string
    labelClass?: string
  }>(),
  {
    modelValue: false,
    label: '',
    disabled: false,
    labelClass: '',
  },
)

const emits = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

const onChange = (event: Event) => {
  const checked = (event.target as HTMLInputElement).checked
  emits('update:modelValue', checked)
}
</script>
