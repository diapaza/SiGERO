/// <reference types="vite/client" />

/** Declaración de tipos para importar componentes `.vue`. */
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<Record<string, unknown>, Record<string, unknown>, unknown>
  export default component
}

/** Variables de entorno de Vite expuestas al frontend. */
interface ImportMetaEnv {
  readonly BASE_URL: string
  readonly APP_NAME: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
