<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[99999] flex items-center justify-center"
      aria-modal="true"
      role="dialog"
      :aria-label="ariaLabel"
    >
      <div class="fixed inset-0 z-[-1] bg-black/40 backdrop-blur-sm" @click="onBackdropClick" />

      <transition name="modal-fade" appear>
        <div
          v-show="isOpen"
          ref="panel"
          :class="['mx-4 w-full outline-none transition-transform', sizeClass]"
          tabindex="-1"
          @keydown.esc.prevent="onEsc"
        >
          <div
            class="mx-auto rounded-2xl bg-white dark:bg-gray-900 shadow-lg overflow-hidden p-6"
            :class="contentClass"
          >
            <header v-if="$slots.header || title" class="px-6 pt-2">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <h3 v-if="title" class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                    {{ title }}
                  </h3>
                  <slot name="header" />
                </div>
                <BaseCloseButton class-name="relative right-0 top-0 shrink-0" @click="close" />
              </div>
            </header>

            <main class="px-6 pb-6">
              <slot name="body">
                <slot />
              </slot>
            </main>

            <footer
              v-if="$slots.footer || $slots.actions"
              class="px-6 py-4 border-t border-gray-100 dark:border-gray-800"
            >
              <div class="flex flex-wrap items-center justify-end gap-3">
                <slot name="footer" />
                <slot name="actions" />
              </div>
            </footer>
          </div>
        </div>
      </transition>
    </div>
  </Teleport>
</template>

/** * Modal reutilizable. * * Se transporta al final del `body` y ofrece tamaños configurables,
cierre * por teclado (Esc) o por clic en el fondo, bloqueo de scroll y gestión del * foco (trampa de
foco y restauración al elemento anterior). */
<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, nextTick, computed } from 'vue'
import BaseCloseButton from '@/components/base/BaseCloseButton.vue'

const props = withDefaults(
  defineProps<{
    /** Controla la visibilidad del modal (v-model). */
    isOpen: boolean
    /** Título mostrado en la cabecera del modal. */
    title?: string
    /** Tamaño del modal (sm, md, lg, xl o fullscreen). */
    size?: 'sm' | 'md' | 'lg' | 'xl' | 'fullscreen'
    /** Etiqueta ARIA descriptiva para accesibilidad. */
    ariaLabel?: string
    /** Permite cerrar el modal al hacer clic en el fondo. */
    closeOnBackdrop?: boolean
    /** Clases CSS adicionales aplicadas al contenido del modal. */
    contentClass?: string
  }>(),
  {
    size: 'md',
    ariaLabel: 'Modal dialog',
    closeOnBackdrop: true,
    contentClass: '',
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza el estado de visibilidad del modal (v-model). */
  (e: 'update:isOpen', val: boolean): void
  /** Se emite al solicitar el cierre del modal. */
  (e: 'close'): void
}>()

const panel = ref<HTMLElement | null>(null)
let lastFocused: Element | null = null

/** Clase de ancho máximo según el tamaño configurado del modal. */
const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'max-w-md'
    case 'md':
      return 'max-w-2xl'
    case 'lg':
      return 'max-w-4xl'
    case 'xl':
      return 'max-w-6xl'
    case 'fullscreen':
      return 'h-full max-w-full'
    default:
      return 'max-w-2xl'
  }
})

/** Bloquea el scroll de la página mientras el modal está abierto. */
const lockScroll = () => document.documentElement.classList.add('overflow-hidden')
/** Restaura el scroll de la página al cerrar el modal. */
const unlockScroll = () => document.documentElement.classList.remove('overflow-hidden')

/** Guarda el elemento con foco previo y enfoca el panel del modal. */
const focusPanel = async () => {
  await nextTick()
  lastFocused = document.activeElement
  panel.value?.focus()
}

/** Restaura el foco al elemento que lo tenía antes de abrir el modal. */
const restoreFocus = () => {
  try {
    if (lastFocused && (lastFocused as HTMLElement).focus) {
      ;(lastFocused as HTMLElement).focus()
    }
  } catch {
    // ignore
  }
}

/** Cierra el modal al hacer clic en el fondo si `closeOnBackdrop` lo permite. */
const onBackdropClick = () => {
  if (props.closeOnBackdrop) close()
}

/** Cierra el modal al presionar la tecla Esc. */
const onEsc = () => close()

/** Emite los eventos de cierre y actualiza el v-model. */
const close = () => {
  emits('update:isOpen', false)
  emits('close')
}

/** Mantiene el foco dentro del panel del modal (trampa de foco). */
const trapFocus = (event: FocusEvent) => {
  const el = panel.value
  if (!el) return
  if (event.target && !el.contains(event.target as Node)) {
    event.stopPropagation()
    el.focus()
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      lockScroll()
      focusPanel()
      document.addEventListener('focusin', trapFocus)
    } else {
      unlockScroll()
      restoreFocus()
      document.removeEventListener('focusin', trapFocus)
    }
  },
)

onMounted(() => {
  if (props.isOpen) {
    lockScroll()
    focusPanel()
  }
})

onBeforeUnmount(() => {
  unlockScroll()
  document.removeEventListener('focusin', trapFocus)
})
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition:
    opacity 0.18s ease,
    transform 0.18s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: translateY(6px) scale(0.995);
}
</style>
