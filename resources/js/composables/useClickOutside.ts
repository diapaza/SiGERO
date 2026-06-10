import { ref, onMounted, onBeforeUnmount, type Ref } from 'vue'

export function useClickOutside(targetRef: Ref<HTMLElement | null>, callback: () => void) {
  function onClick(e: MouseEvent) {
    if (targetRef.value && !targetRef.value.contains(e.target as Node)) {
      callback()
    }
  }

  onMounted(() => document.addEventListener('mousedown', onClick))
  onBeforeUnmount(() => document.removeEventListener('mousedown', onClick))
}
