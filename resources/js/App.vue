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
import ThemeProvider from './components/layout/ThemeProvider.vue'
import SidebarProvider from './components/layout/SidebarProvider.vue'
import BaseToast from './components/base/BaseToast.vue'
import GlobalDialog from './components/shared/GlobalDialog.vue'
</script>
