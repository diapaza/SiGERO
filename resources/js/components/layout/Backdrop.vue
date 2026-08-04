<template>
  <div
    v-if="isMobileOpen"
    class="fixed inset-0 bg-gray-900/50 z-9999 lg:hidden"
    @click="toggleMobileSidebar"
  />
</template>

/** * Fondo translúcido que se superpone a la pantalla cuando el menú lateral móvil * está abierto.
Al hacer clic sobre él cierra el menú lateral móvil. */
<script setup lang="ts">
import { useSidebar } from '@/composables/useSidebar'
const { toggleMobileSidebar, isMobileOpen } = useSidebar()
</script>
