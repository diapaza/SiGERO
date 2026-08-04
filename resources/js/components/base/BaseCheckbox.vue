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

/** * Casilla de verificación reutilizable. * * Renderiza un checkbox personalizado con etiqueta
opcional, soporte para * enlace bidireccional con `v-model` y estados de deshabilitado. */
<script setup lang="ts">
import CheckIcon from '@/icons/CheckIcon.vue'

withDefaults(
  defineProps<{
    /** Valor del checkbox (v-model). */
    modelValue?: boolean
    /** Texto de la etiqueta mostrada junto al checkbox. */
    label?: string
    /** Deshabilita la casilla e impide su interacción. */
    disabled?: boolean
    /** Identificador HTML asociado a la casilla y a la etiqueta. */
    id?: string
    /** Atributo `name` del input de la casilla. */
    name?: string
    /** Clases CSS adicionales aplicadas a la etiqueta. */
    labelClass?: string
  }>(),
  {
    modelValue: false,
    label: '',
    disabled: false,
    labelClass: '',
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza el valor del checkbox (v-model). */
  (e: 'update:modelValue', value: boolean): void
}>()

/** Emite el nuevo estado de la casilla a partir del evento `change`. */
const onChange = (event: Event) => {
  const checked = (event.target as HTMLInputElement).checked
  emits('update:modelValue', checked)
}
</script>
