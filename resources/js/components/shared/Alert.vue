<template>
  <div :class="['rounded-xl border p-4', variantClasses[variant].container]">
    <div class="flex items-start gap-3">
      <div :class="['-mt-0.5', variantClasses[variant].icon]">
        <component :is="icons[variant]" />
      </div>

      <div>
        <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
          {{ title }}
        </h4>

        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ message }}
        </p>

        <Link
          v-if="showLink"
          :href="linkHref"
          class="inline-block mt-3 text-sm font-medium text-gray-500 underline dark:text-gray-400"
        >
          {{ linkText }}
        </Link>
      </div>
    </div>
  </div>
</template>

/** * Alerta informativa con variantes visuales (éxito, error, advertencia o * información). Muestra
un icono, un título, un mensaje y un enlace opcional. */
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { SuccessIcon, ErrorIcon, WarningIcon, InfoCircleIcon } from '@/icons'

interface AlertProps {
  /** Variante visual de la alerta: 'success' | 'error' | 'warning' | 'info'. */
  variant: 'success' | 'error' | 'warning' | 'info'
  /** Título de la alerta. */
  title: string
  /** Mensaje descriptivo de la alerta. */
  message: string
  /** Indica si se muestra el enlace adicional. */
  showLink?: boolean
  /** Ruta de destino del enlace adicional. */
  linkHref?: string
  /** Texto del enlace adicional. */
  linkText?: string
}

withDefaults(defineProps<AlertProps>(), {
  showLink: false,
  linkHref: '#',
  linkText: 'Learn more',
})

const variantClasses = {
  success: {
    container: 'border-success-500 bg-success-50 dark:border-success-500/30 dark:bg-success-500/15',
    icon: 'text-success-500',
  },
  error: {
    container: 'border-error-500 bg-error-50 dark:border-error-500/30 dark:bg-error-500/15',
    icon: 'text-error-500',
  },
  warning: {
    container: 'border-warning-500 bg-warning-50 dark:border-warning-500/30 dark:bg-warning-500/15',
    icon: 'text-warning-500',
  },
  info: {
    container:
      'border-blue-light-500 bg-blue-light-50 dark:border-blue-light-500/30 dark:bg-blue-light-500/15',
    icon: 'text-blue-light-500',
  },
}

const icons = {
  success: SuccessIcon,
  error: ErrorIcon,
  warning: WarningIcon,
  info: InfoCircleIcon,
}
</script>
