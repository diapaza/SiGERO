<template>
  <slot />
</template>

/** * Proveedor del contexto global de la barra lateral. * * Crea y provee el contexto `useSidebar`
a todos los componentes descendientes a * través de `useSidebarProvider()`. Además, suscribe el
cierre del menú móvil al * hook `before` del router de Inertia para que la barra se cierre al
navegar. */
<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { useSidebarProvider } from '@/composables/useSidebar'

const { closeMobileSidebar } = useSidebarProvider()

// Cierra el menú lateral móvil antes de cada navegación de Inertia.
router.on('before', () => {
  closeMobileSidebar()
})
</script>
