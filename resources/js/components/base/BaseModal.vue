<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[99999] flex items-center justify-center" 
      aria-modal="true"
      role="dialog"
      :aria-label="ariaLabel"
    >
      <div class="fixed inset-0 z-[-1] bg-black/40 backdrop-blur-sm" @click="onBackdropClick"></div>

      <transition name="modal-fade" appear>
        <div
          v-show="isOpen"
          ref="panel"
          :class="['mx-4 w-full outline-none transition-transform', sizeClass]"
          @keydown.esc.prevent="onEsc"
          tabindex="-1"
        >
          <div
            class="mx-auto rounded-2xl bg-white dark:bg-gray-900 shadow-lg overflow-hidden p-6"
            :class="contentClass"
          >
            <header v-if="$slots.header || title" class="px-6 pt-2">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <h3 v-if="title" class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ title }}</h3>
                  <slot name="header" />
                </div>
                <BaseCloseButton @click="close" className="relative right-0 top-0 shrink-0" />
              </div>
            </header>

            <main class="px-6 pb-6">
              <slot name="body">
                <slot />
              </slot>
            </main>

            <footer v-if="$slots.footer || $slots.actions" class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
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

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, nextTick, computed } from 'vue'
import BaseCloseButton from '@/components/base/BaseCloseButton.vue'

const props = withDefaults(defineProps<{
  isOpen: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg' | 'xl' | 'fullscreen'
  ariaLabel?: string
  closeOnBackdrop?: boolean
  contentClass?: string
}>(), {
  size: 'md',
  ariaLabel: 'Modal dialog',
  closeOnBackdrop: true,
  contentClass: '',
})

const emits = defineEmits<{
  (e: 'update:isOpen', val: boolean): void
  (e: 'close'): void
}>()

const panel = ref<HTMLElement | null>(null)
let lastFocused: Element | null = null

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

const lockScroll = () => document.documentElement.classList.add('overflow-hidden')
const unlockScroll = () => document.documentElement.classList.remove('overflow-hidden')

const focusPanel = async () => {
  await nextTick()
  lastFocused = document.activeElement
  panel.value?.focus()
}

const restoreFocus = () => {
  try {
    if (lastFocused && (lastFocused as HTMLElement).focus) {
      ;(lastFocused as HTMLElement).focus()
    }
  } catch (e) {
    // ignore
  }
}

const onBackdropClick = () => {
  if (props.closeOnBackdrop) close()
}

const onEsc = () => close()

const close = () => {
  emits('update:isOpen', false)
  emits('close')
}

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
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: translateY(6px) scale(0.995);
}
</style>
