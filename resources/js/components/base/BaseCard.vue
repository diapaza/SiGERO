<template>
  <div
    :class="[
      'rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]',
      cardClass,
    ]"
  >
    <div
      v-if="$slots.header || title"
      class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"
    >
      <div class="flex flex-col gap-2 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <h3 v-if="title" class="text-base font-medium text-gray-800 dark:text-white/90">
            {{ title }}
          </h3>
          <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ description }}
          </p>
        </div>
        <div>
          <slot name="header" />
        </div>
      </div>
    </div>

    <div :class="['p-4 border-t border-gray-100 dark:border-gray-800 sm:p-6', contentClass]">
      <slot />
    </div>

    <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
      <slot name="footer" />
    </div>
  </div>
</template>

/** * Tarjeta contenedora reutilizable. * * Proporciona una estructura con cabecera opcional (título
y descripción), * cuerpo principal y pie, permitiendo personalizar las clases CSS de cada * sección
mediante props y slots. */
<script setup lang="ts">
withDefaults(
  defineProps<{
    /** Título mostrado en la cabecera de la tarjeta. */
    title?: string
    /** Descripción mostrada bajo el título de la cabecera. */
    description?: string
    /** Clases CSS adicionales aplicadas al contenedor de la tarjeta. */
    cardClass?: string
    /** Clases CSS adicionales aplicadas al cuerpo (contenido). */
    contentClass?: string
  }>(),
  {
    cardClass: '',
    contentClass: '',
  },
)
</script>
