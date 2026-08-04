import { reactive } from 'vue'

type DialogType = 'alert' | 'confirm' | 'prompt'

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

function close(value?: any) {
  state.resolve?.(value)
  state.resolve = null
  state.isOpen = false
  setTimeout(() => {
    state.type = null
  }, 300)
}

export function useDialog() {
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
