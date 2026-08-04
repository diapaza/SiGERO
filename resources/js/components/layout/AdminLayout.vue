<template>
  <div class="min-h-screen xl:flex">
    <app-sidebar />
    <Backdrop />
    <div
      class="flex-1 transition-all duration-300 ease-in-out"
      :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']"
    >
      <app-header />
      <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 dark:bg-gray-900">
        <slot />
      </div>
    </div>
  </div>
</template>

/** * Layout principal de las páginas autenticadas de la aplicación. * * Estructura el esqueleto del
panel: la barra lateral (AppSidebar), el fondo * translúcido en móvil (Backdrop) y la cabecera
(AppHeader). Desplaza el área de * contenido según el estado de la barra lateral (expandida,
contraída o en hover). * Expone un slot para el contenido de cada página. */
<script setup>
import AppSidebar from './AppSidebar.vue'
import AppHeader from './AppHeader.vue'
import { useSidebar } from '@/composables/useSidebar'
import Backdrop from './Backdrop.vue'
// Estado compartido de la barra lateral: expandida o contraída en escritorio y en hover.
const { isExpanded, isHovered } = useSidebar()
</script>
