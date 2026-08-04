<template>
  <textarea
    :id="id"
    :name="name"
    :rows="rows"
    :placeholder="placeholder"
    :disabled="disabled"
    :class="textareaClasses"
    :value="modelValue"
    @input="onInput"
    @change="onChange"
  />
</template>

/** * Área de texto reutilizable. * * Soporta estado visual (default/error/success), número de filas
* configurable y enlace bidireccional con `v-model`. */
<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    /** Valor del textarea (v-model). */
    modelValue?: string
    /** Número de filas visibles del textarea. */
    rows?: number
    /** Texto de marcador de posición mostrado cuando está vacío. */
    placeholder?: string
    /** Deshabilita el textarea e impide su edición. */
    disabled?: boolean
    /** Identificador HTML del textarea. */
    id?: string
    /** Atributo `name` del textarea. */
    name?: string
    /** Estado visual del campo (default, error o success). */
    state?: 'default' | 'error' | 'success'
    /** Clases CSS adicionales aplicadas al textarea. */
    className?: string
  }>(),
  {
    modelValue: '',
    rows: 4,
    placeholder: '',
    disabled: false,
    state: 'default',
    className: '',
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza el valor del textarea (v-model). */
  (e: 'update:modelValue', value: string): void
  /** Se emite al dispararse el evento `change` del textarea. */
  (e: 'change', event: Event): void
}>()

const stateClasses = {
  default:
    'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800',
  error:
    'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800',
  success:
    'border-success-300 focus:border-success-300 focus:ring-success-500/10 dark:border-success-700 dark:focus:border-success-800',
}

const textareaClasses = computed(() => [
  'w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
  stateClasses[props.state],
  props.className,
])

/** Emite el nuevo valor del textarea (v-model) al escribir. */
const onInput = (event: Event) => {
  const value = (event.target as HTMLTextAreaElement).value
  emits('update:modelValue', value)
}

/** Reenvía el evento `change` del textarea. */
const onChange = (event: Event) => {
  emits('change', event)
}
</script>
