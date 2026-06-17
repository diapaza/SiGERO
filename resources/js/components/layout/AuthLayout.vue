<template>
  <FullScreenLayout>
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
      <div
        class="relative flex flex-col justify-center w-full h-screen lg:flex-row dark:bg-gray-900"
      >
        <div class="flex flex-col flex-1 w-full lg:w-1/2">
          <div v-if="page.props.auth?.user" class="w-full max-w-md pt-10 mx-auto">
            <Link
              :href="backTo"
              class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
            >
              <ChevronLeftIcon class="stroke-current" />
              {{ backLabel }}
            </Link>
          </div>
          <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
            <div>
              <div class="mb-5 sm:mb-8">
                <h1
                  class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md"
                >
                  {{ title }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ description }}
                </p>
              </div>
              <div>
                <slot />
              </div>
            </div>
          </div>
        </div>
        <div
          class="relative items-center hidden w-full h-full lg:w-1/2 bg-brand-950 dark:bg-white/5 lg:grid"
        >
          <div class="flex items-center justify-center z-1">
            <CommonGridShape />
            <div class="flex flex-col items-center max-w-xs">
              <Link href="/" class="block mb-4">
                <img width="231" height="48" src="/images/logo/auth-logo.svg" alt="Logo" />
              </Link>
              <p class="text-center text-gray-400 dark:text-white/60">
                Bienvenido al Sistema de Gestion de Equipos y Recursos Operativos de la Subunidad de
                Redes y Comunicaciones.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FullScreenLayout>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import FullScreenLayout from '@/components/layout/FullScreenLayout.vue'
import CommonGridShape from '@/components/shared/CommonGridShape.vue'
import ChevronLeftIcon from '@/icons/ChevronLeftIcon.vue'

withDefaults(
  defineProps<{
    title?: string
    description?: string
    backTo?: string
    backLabel?: string
  }>(),
  {
    title: '',
    description: '',
    backTo: '/',
    backLabel: 'Back to dashboard',
  },
)

const page = usePage()
</script>
