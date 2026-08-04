<template>
  <BaseCard :title="title" :description="desc" :card-class="className" content-class="space-y-5">
    <template #header>
      <slot name="header" />
    </template>
    <slot />
  </BaseCard>
</template>

/** * Tarjeta reutilizable que envuelve a BaseCard con un título, una descripción y * espacios
verticales. Expone un slot por defecto para el contenido y un slot * 'header' para elementos
adicionales en la cabecera. */
<script setup lang="ts">
import BaseCard from '@/components/base/BaseCard.vue'

interface Props {
  /** Título mostrado en la tarjeta. */
  title: string
  /** Clases CSS adicionales para el contenedor de la tarjeta. */
  className?: string
  /** Descripción corta que acompaña al título. */
  desc?: string
}

defineProps<Props>()
</script>
