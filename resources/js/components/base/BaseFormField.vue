<template>
  <div :class="['space-y-1.5', wrapperClass]">
    <label
      v-if="label || $slots.label"
      :for="labelFor"
      :class="[
        'block text-sm font-medium',
        disabled ? 'text-gray-300 dark:text-white/15' : 'text-gray-700 dark:text-gray-400',
        labelClass,
      ]"
    >
      <slot name="label">
        {{ label }}
      </slot>
      <span v-if="required" class="text-error-500 ml-0.5">*</span>
    </label>

    <slot />

    <p v-if="error" class="text-theme-xs text-error-500">
      {{ error }}
    </p>
    <p v-else-if="hint" class="text-theme-xs text-gray-500 dark:text-gray-400">
      {{ hint }}
    </p>
  </div>
</template>

/** * Envoltorio de campos de formulario reutilizable. * * Renderiza la etiqueta (con indicador de
obligatoriedad), el campo a través * de un slot y un mensaje de error o ayuda, agrupando los
elementos con un * espaciado uniforme. */
<script setup lang="ts">
withDefaults(
  defineProps<{
    /** Texto de la etiqueta mostrada sobre el campo. */
    label?: string
    /** Atributo `for` de la etiqueta, que apunta al id del campo. */
    labelFor?: string
    /** Marca el campo como obligatorio (muestra un asterisco). */
    required?: boolean
    /** Mensaje de error mostrado bajo el campo. */
    error?: string
    /** Mensaje de ayuda mostrado bajo el campo cuando no hay error. */
    hint?: string
    /** Deshabilita el campo (atenúa la etiqueta). */
    disabled?: boolean
    /** Clases CSS adicionales aplicadas a la etiqueta. */
    labelClass?: string
    /** Clases CSS adicionales aplicadas al contenedor del campo. */
    wrapperClass?: string
  }>(),
  {
    label: '',
    required: false,
    disabled: false,
    labelClass: '',
    wrapperClass: '',
  },
)
</script>
