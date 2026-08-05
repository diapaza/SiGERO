<template>
  <div :class="['relative w-full', wrapperClass]">
    <div
      v-if="$slots.prepend"
      class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
    >
      <slot name="prepend" />
    </div>

    <input
      :id="id"
      :name="name"
      :type="type"
      :placeholder="placeholder"
      :disabled="disabled"
      :maxlength="maxlength"
      :value="modelValue"
      :class="inputClasses"
      @input="onInput"
      @change="onChange"
      @blur="onBlur"
    />

    <div v-if="$slots.append" class="absolute inset-y-0 right-0 flex items-center pr-3">
      <slot name="append" />
    </div>
  </div>
</template>

/** * Campo de texto reutilizable. * * Soporta estado visual (default/error/success), iconos
`prepend`/`append` * mediante slots y enlace bidireccional con `v-model`. */
<script setup lang="ts">
import { computed, useSlots } from 'vue'

const props = withDefaults(
  defineProps<{
    /** Valor del input (v-model). */
    modelValue?: string | number
    /** Tipo HTML del input (text, number, email, etc.). */
    type?: string
    /** Texto de marcador de posición mostrado cuando el campo está vacío. */
    placeholder?: string
    /** Deshabilita el input e impide su edición. */
    disabled?: boolean
    /** Identificador HTML del input. */
    id?: string
    /** Atributo `name` del input. */
    name?: string
    /** Estado visual del campo (default, error o success). */
    state?: 'default' | 'error' | 'success'
    /** Clases CSS adicionales aplicadas al input. */
    className?: string
    /** Clases CSS adicionales aplicadas al contenedor. */
    wrapperClass?: string
    /** Longitud máxima de caracteres permitida en el input. */
    maxlength?: number
  }>(),
  {
    modelValue: '',
    type: 'text',
    placeholder: '',
    disabled: false,
    state: 'default',
    className: '',
    wrapperClass: '',
    maxlength: undefined,
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza el valor del input (v-model). */
  (e: 'update:modelValue', value: string | number): void
  /** Se emite al dispararse el evento `change` del input. */
  (e: 'change', event: Event): void
  /** Se emite al dispararse el evento `blur` del input. */
  (e: 'blur', event: FocusEvent): void
}>()

const slots = useSlots()

/** Indica si el slot `prepend` está definido. */
const hasPrepend = computed(() => !!slots.prepend)
/** Indica si el slot `append` está definido. */
const hasAppend = computed(() => !!slots.append)

const stateClasses = {
  default:
    'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800',
  error:
    'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800',
  success:
    'border-success-300 focus:border-success-300 focus:ring-success-500/10 dark:border-success-700 dark:focus:border-success-800',
}

const inputClasses = computed(() => [
  'h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
  stateClasses[props.state],
  hasPrepend.value ? 'pl-11' : '',
  hasAppend.value ? 'pr-11' : '',
  props.disabled ? 'bg-gray-100 cursor-not-allowed opacity-60 dark:bg-gray-800' : '',
  props.className,
])

/** Emite el nuevo valor del input (v-model) al escribir. */
const onInput = (event: Event) => {
  const value = (event.target as HTMLInputElement).value
  emits('update:modelValue', value)
}

/** Reenvía el evento `change` del input. */
const onChange = (event: Event) => {
  emits('change', event)
}

/** Reenvía el evento `blur` del input. */
const onBlur = (event: FocusEvent) => {
  emits('blur', event)
}
</script>
