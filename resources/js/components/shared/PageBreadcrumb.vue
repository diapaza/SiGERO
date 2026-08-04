<template>
  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageTitle">
      {{ pageTitle }}
    </h2>
    <nav>
      <ol class="flex items-center gap-1.5">
        <li>
          <Link
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
            href="/"
          >
            Home
            <ChevronRightIcon class="stroke-current" />
          </Link>
        </li>
        <li class="text-sm text-gray-800 dark:text-white/90">
          {{ pageTitle }}
        </li>
      </ol>
    </nav>
  </div>
</template>

/** * Migas de pan (breadcrumb) de encabezado de página. * * Muestra el título de la página y la
ruta de navegación desde la página de * inicio hasta la página actual. */
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import ChevronRightIcon from '@/icons/ChevronRightIcon.vue'

interface BreadcrumbProps {
  /** Título de la página actual mostrado en el breadcrumb. */
  pageTitle: string
}

defineProps<BreadcrumbProps>()
</script>
