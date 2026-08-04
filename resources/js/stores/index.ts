import { createPinia } from 'pinia'

/**
 * Instancia global de Pinia.
 *
 * Se registra en `app.ts` (`app.use(pinia)`). Actualmente no hay stores
 * definidos; se deja la infraestructura lista para estados globales futuros.
 */
const pinia = createPinia()

export default pinia
