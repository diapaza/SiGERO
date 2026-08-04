import { route as ziggyRoute } from 'ziggy-js'

declare global {
  /**
   * Helper global de Ziggy.
   *
   * Expone `route()` en archivos `.ts` y `.vue` (el JSON de rutas se inyecta
   * con la directiva `@routes` en `resources/views/app.blade.php`).
   */
  const route: typeof ziggyRoute
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    /** Helper global `route()` disponible dentro de los templates de Vue. */
    route: typeof ziggyRoute
  }
}

export {}
