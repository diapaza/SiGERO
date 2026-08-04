/** * Contenedor de notificaciones (toasts). * * Configura el `Toaster` de vue-sonner en la posición
inferior derecha con * botón de cierre, duración fija y un máximo de notificaciones visibles. */
<template>
  <Toaster
    position="bottom-right"
    :close-button="true"
    :duration="4000"
    rich-colors
    :visible-toasts="6"
  />
</template>

<script setup lang="ts">
import { Toaster } from 'vue-sonner'
</script>
