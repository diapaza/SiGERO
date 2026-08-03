<template>
  <ThemeProvider>
    <SidebarProvider>
      <slot />
      <BaseToast />
      <GlobalDialog />
    </SidebarProvider>
  </ThemeProvider>
</template>

<script setup lang="ts">
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import ThemeProvider from './components/layout/ThemeProvider.vue'
import SidebarProvider from './components/layout/SidebarProvider.vue'
import BaseToast from './components/base/BaseToast.vue'
import GlobalDialog from './components/shared/GlobalDialog.vue'

const page = usePage()

watch(
  () => (page.props as any)?.flash?.success,
  (message) => {
    if (message) toast.success(message)
  },
)

watch(
  () => (page.props as any)?.flash?.error,
  (message) => {
    if (message) toast.error(message)
  },
)
</script>
