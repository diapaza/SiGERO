import { inject, type ComputedRef } from 'vue'

/** Symbol de inyección del tema (claro/oscuro). */
export const THEME_KEY = Symbol('theme')

/** Contexto del tema expuesto por `ThemeProvider`. */
export interface ThemeContext {
  /** `true` si el tema oscuro está activo. */
  isDarkMode: ComputedRef<boolean>
  /** Alterna entre tema claro y oscuro. */
  toggleTheme: () => void
}

/**
 * Consume el contexto del tema (claro/oscuro) dentro de un `ThemeProvider`.
 *
 * @throws Error si se usa fuera de un `ThemeProvider`.
 */
export function useTheme(): ThemeContext {
  const theme = inject<ThemeContext>(THEME_KEY)
  if (!theme) {
    throw new Error('useTheme must be used within a ThemeProvider')
  }
  return theme
}
