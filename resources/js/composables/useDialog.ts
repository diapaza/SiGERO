import { reactive } from 'vue'

/** Tipo de diálogo soportado por el composable. */
type DialogType = 'alert' | 'confirm' | 'prompt'

/** Estado global compartido del diálogo. */
interface State {
  isOpen: boolean
  type: DialogType | null
  title: string
  description: string
  icon: string | undefined
  confirmLabel: string
  cancelLabel: string
  destructive: boolean
  placeholder: string
  initialValue: string
  resolve: ((value: any) => void) | null
}

const state = reactive<State>({
  isOpen: false,
  type: null,
  title: '',
  description: '',
  icon: undefined,
  confirmLabel: 'OK',
  cancelLabel: 'Cancel',
  destructive: false,
  placeholder: '',
  initialValue: '',
  resolve: null,
})

/**
 * Abre un diálogo y devuelve una promesa resuelta al cerrar.
 *
 * @template T Tipo del valor con el que se resuelve la promesa.
 * @param type Tipo de diálogo (`confirm`, `alert`, `prompt`).
 * @param options Opciones de presentación del diálogo.
 */
function open<T>(type: DialogType, options: Record<string, any>): Promise<T> {
  return new Promise((resolve) => {
    state.type = type
    state.title = options.title ?? ''
    state.description = options.description ?? ''
    state.icon = options.icon
    state.confirmLabel = options.confirmLabel ?? (type === 'confirm' ? 'Confirm' : 'OK')
    state.cancelLabel = options.cancelLabel ?? 'Cancel'
    state.destructive = options.destructive ?? false
    state.placeholder = options.placeholder ?? ''
    state.initialValue = options.initialValue ?? ''
    state.resolve = resolve as (value: any) => void
    state.isOpen = true
  })
}

/**
 * Cierra el diálogo resolviendo la promesa pendiente con `value`.
 *
 * @param value Valor con el que se resuelve la promesa.
 */
function close(value?: any) {
  state.resolve?.(value)
  state.resolve = null
  state.isOpen = false
  setTimeout(() => {
    state.type = null
  }, 300)
}

/**
 * Composable de diálogos de confirmación/alert/prompt.
 *
 * Gestiona un estado global compartido (`state`) que consume el componente
 * `GlobalDialog`. Las vistas usan `confirm()` para pedir confirmación antes
 * de acciones destructivas.
 */
export function useDialog() {
  /**
   * Pide confirmación (Sí/No).
   *
   * @returns Promesa que resuelve a `true` si se confirma.
   */
  function confirm(
    options: {
      title?: string
      description?: string
      icon?: string
      confirmLabel?: string
      cancelLabel?: string
      destructive?: boolean
    } = {},
  ): Promise<boolean> {
    return open<boolean>('confirm', options)
  }

  /**
   * Muestra una alerta informativa.
   *
   * @returns Promesa que resuelve al cerrar.
   */
  function alert(
    options: {
      title?: string
      description?: string
      icon?: string
      confirmLabel?: string
    } = {},
  ): Promise<void> {
    return open<void>('alert', options)
  }

  /**
   * Pide un valor de texto.
   *
   * @returns Promesa que resuelve al texto ingresado o `null` si se cancela.
   */
  function prompt(
    options: {
      title?: string
      description?: string
      icon?: string
      confirmLabel?: string
      cancelLabel?: string
      placeholder?: string
      initialValue?: string
    } = {},
  ): Promise<string | null> {
    return open<string | null>('prompt', options)
  }

  return { state, confirm, alert, prompt, close }
}
