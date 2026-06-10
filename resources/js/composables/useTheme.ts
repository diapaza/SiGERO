import { inject, type ComputedRef } from 'vue'

export const THEME_KEY = Symbol('theme')

export interface ThemeContext {
  isDarkMode: ComputedRef<boolean>
  toggleTheme: () => void
}

export function useTheme(): ThemeContext {
  const theme = inject<ThemeContext>(THEME_KEY)
  if (!theme) {
    throw new Error('useTheme must be used within a ThemeProvider')
  }
  return theme
}
