<template>
  <slot />
</template>

/** * Proveedor del contexto global de tema. * * Gestiona el tema claro/oscuro, lo persiste en
`localStorage` y lo aplica al * elemento raíz del documento (clase `dark`). Provee el contexto
`useTheme` * ({ isDarkMode, toggleTheme }) a todos los componentes descendientes. */
<script setup lang="ts">
import { ref, provide, onMounted, watch, computed } from 'vue'
import { THEME_KEY, type ThemeContext } from '@/composables/useTheme'

// Tema soportado por la aplicación: claro u oscuro.
type Theme = 'light' | 'dark'

const theme = ref<Theme>('light')
const isInitialized = ref(false)

/** Indica si el tema activo es el oscuro. */
const isDarkMode = computed(() => theme.value === 'dark')

/** Conmuta entre el tema claro y el oscuro. */
const toggleTheme = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
}

/** Restaura el tema guardado en localStorage (o claro por defecto). */
onMounted(() => {
  const savedTheme = localStorage.getItem('theme') as Theme | null
  const initialTheme = savedTheme || 'light'

  theme.value = initialTheme
  isInitialized.value = true
})

/** Persiste el tema y sincroniza la clase `dark` del documento al cambiar. */
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
