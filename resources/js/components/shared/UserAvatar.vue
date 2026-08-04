<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 48 48"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <defs>
      <linearGradient :id="gradientId" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" :stop-color="gradient.from" />
        <stop offset="100%" :stop-color="gradient.to" />
      </linearGradient>
    </defs>
    <circle cx="24" cy="24" r="24" :fill="`url(#${gradientId})`" />
    <text
      x="24"
      y="24"
      text-anchor="middle"
      dominant-baseline="central"
      fill="white"
      font-size="18"
      font-weight="700"
      font-family="system-ui, -apple-system, sans-serif"
    >
      {{ initials }}
    </text>
  </svg>
</template>

/** * Avatar de usuario generado de forma determinista. * * Renderiza un círculo SVG con un
degradado y las iniciales del nombre. El color * del degradado se deriva del nombre, de modo que
cada usuario tiene un avatar * consistente. */
<script setup lang="ts">
import { computed } from 'vue'
import { getInitials, getGradientColors } from '@/composables/useUserAvatar'

const props = withDefaults(
  defineProps<{
    /** Nombre completo del usuario para calcular iniciales y color. */
    name: string
    /** Tamaño del avatar en píxeles. */
    size?: number
  }>(),
  {
    size: 44,
  },
)

/** Iniciales del usuario mostradas en el centro del avatar. */
const initials = computed(() => getInitials(props.name))
/** Colores del degradado derivados del nombre. */
const gradient = computed(() => getGradientColors(props.name))

/** Identificador único del degradado SVG. */
const gradientId = `ag-${Math.random().toString(36).substring(2, 10)}`
</script>
