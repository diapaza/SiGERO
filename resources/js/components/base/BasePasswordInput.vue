<template>
  <BaseInput
    :id="id"
    :model-value="modelValue"
    :type="showPassword ? 'text' : 'password'"
    :placeholder="placeholder"
    :disabled="disabled"
    :name="name"
    :state="state"
    :maxlength="maxlength"
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

/** * Campo de contraseña reutilizable. * * Extiende `BaseInput` añadiendo un botón para alternar la
visibilidad de la * contraseña entre `password` y `text`, manteniendo el enlace bidireccional * con
`v-model` y los estados visuales del campo base. */
<script setup lang="ts">
import { ref } from 'vue'
import BaseInput from './BaseInput.vue'
import EyeIcon from '@/icons/EyeIcon.vue'
import EyeOffIcon from '@/icons/EyeOffIcon.vue'

withDefaults(
  defineProps<{
    /** Valor de la contraseña (v-model). */
    modelValue?: string | number
    /** Texto de marcador de posición mostrado cuando el campo está vacío. */
    placeholder?: string
    /** Deshabilita el campo e impide su edición. */
    disabled?: boolean
    /** Identificador HTML del campo. */
    id?: string
    /** Atributo `name` del campo. */
    name?: string
    /** Estado visual del campo (default, error o success). */
    state?: 'default' | 'error' | 'success'
    /** Clases CSS adicionales aplicadas al input. */
    className?: string
    /** Clases CSS adicionales aplicadas al contenedor. */
    wrapperClass?: string
    /** Longitud máxima de caracteres permitida en el campo. */
    maxlength?: number
  }>(),
  {
    modelValue: '',
    placeholder: '',
    disabled: false,
    state: 'default',
    className: '',
    wrapperClass: '',
    maxlength: undefined,
  },
)

// Emite:
const emit = defineEmits<{
  /** Actualiza el valor de la contraseña (v-model). */
  (e: 'update:modelValue', value: string | number): void
  /** Se emite al dispararse el evento `change` del input. */
  (e: 'change', event: Event): void
  /** Se emite al dispararse el evento `blur` del input. */
  (e: 'blur', event: FocusEvent): void
}>()

const showPassword = ref(false)

/** Alterna la visibilidad de la contraseña. */
const toggleVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>
