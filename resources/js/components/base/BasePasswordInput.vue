<template>
  <BaseInput
    :id="id"
    :model-value="modelValue"
    :type="showPassword ? 'text' : 'password'"
    :placeholder="placeholder"
    :disabled="disabled"
    :name="name"
    :state="state"
    :class-name="className"
    :wrapper-class="wrapperClass"
    @update:model-value="emit('update:modelValue', $event)"
    @change="emit('change', $event)"
    @blur="emit('blur', $event)"
  >
    <template v-if="$slots.prepend" #prepend>
      <slot name="prepend" />
    </template>
    <template #append>
      <span
        class="cursor-pointer"
        role="button"
        :aria-label="showPassword ? 'Hide password' : 'Show password'"
        @click="toggleVisibility"
      >
        <EyeOffIcon v-if="!showPassword" class="fill-gray-500 dark:fill-gray-400" />
        <EyeIcon v-else class="fill-gray-500 dark:fill-gray-400" />
      </span>
    </template>
  </BaseInput>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseInput from './BaseInput.vue'
import EyeIcon from '@/icons/EyeIcon.vue'
import EyeOffIcon from '@/icons/EyeOffIcon.vue'

withDefaults(
  defineProps<{
    modelValue?: string | number
    placeholder?: string
    disabled?: boolean
    id?: string
    name?: string
    state?: 'default' | 'error' | 'success'
    className?: string
    wrapperClass?: string
  }>(),
  {
    modelValue: '',
    placeholder: '',
    disabled: false,
    state: 'default',
    className: '',
    wrapperClass: '',
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void
  (e: 'change', event: Event): void
  (e: 'blur', event: FocusEvent): void
}>()

const showPassword = ref(false)

const toggleVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>
