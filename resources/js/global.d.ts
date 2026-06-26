import { route as ziggyRoute } from 'ziggy-js'

declare global {
  // Esto le dice a VS Code que "route" existe globalmente en archivos .ts y .vue
  const route: typeof ziggyRoute
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    // Esto le dice a VS Code que "route" existe dentro de los <template> de Vue
    route: typeof ziggyRoute
  }
}

export {}
