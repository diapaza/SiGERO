<template>
  <slot />
</template>

<script setup lang="ts">
import { ref, provide, onMounted, watch, computed } from 'vue'
import { THEME_KEY, type ThemeContext } from '@/composables/useTheme'

type Theme = 'light' | 'dark'

const theme = ref<Theme>('light')
const isInitialized = ref(false)

const isDarkMode = computed(() => theme.value === 'dark')

const toggleTheme = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme') as Theme | null
  const initialTheme = savedTheme || 'light'

  theme.value = initialTheme
  isInitialized.value = true
})

watch([theme, isInitialized], ([newTheme, newIsInitialized]) => {
  if (newIsInitialized) {
    localStorage.setItem('theme', newTheme)
    if (newTheme === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }
})

const context: ThemeContext = { isDarkMode, toggleTheme }
provide(THEME_KEY, context)
</script>
