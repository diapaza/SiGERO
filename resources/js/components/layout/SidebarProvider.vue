<template>
  <slot />
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { useSidebarProvider } from '@/composables/useSidebar'

const { closeMobileSidebar } = useSidebarProvider()

router.on('before', () => {
  closeMobileSidebar()
})
</script>
