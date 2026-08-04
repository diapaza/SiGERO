<template>
  <Link href="/" class="lg:hidden">
    <img class="dark:hidden" src="/images/logo/logo.svg" alt="Logo" />
    <img class="hidden dark:block" src="/images/logo/logo-dark.svg" alt="Logo" />
  </Link>
</template>

/** * Logo de la aplicación mostrado en la cabecera, visible únicamente en pantallas * pequeñas
(lg:hidden). Enlaza a la página de inicio y adapta la imagen según el * tema activo. */
<script setup>
import { Link } from '@inertiajs/vue3'
</script>
