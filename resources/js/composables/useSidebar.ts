import { ref, computed, onMounted, onUnmounted, provide, inject } from 'vue'
import type { Ref } from 'vue'

/** Contexto del sidebar expuesto por `useSidebarProvider` y consumido por `useSidebar`. */
interface SidebarContextType {
  isExpanded: Ref<boolean>
  isMobileOpen: Ref<boolean>
  isHovered: Ref<boolean>
  activeItem: Ref<string | null>
  openSubmenu: Ref<string | null>
  toggleSidebar: () => void
  toggleMobileSidebar: () => void
  closeMobileSidebar: () => void
  setIsHovered: (isHovered: boolean) => void
  setActiveItem: (item: string | null) => void
  toggleSubmenu: (item: string) => void
}

/** Symbol de inyección del contexto del sidebar. */
const SidebarSymbol = Symbol()

/**
 * Provee el estado del sidebar (expandido/collapsado, móvil, hover y
 * submenús) a los componentes del layout. Se usa en `SidebarProvider`.
 *
 * @returns Contexto del sidebar con sus estados y acciones.
 */
export function useSidebarProvider() {
  const isExpanded = ref(true)
  const isMobileOpen = ref(false)
  const isMobile = ref(false)
  const isHovered = ref(false)
  const activeItem = ref<string | null>(null)
  const openSubmenu = ref<string | null>(null)

  /** Detecta si la ventana es móvil (< 768px) y ajusta el estado. */
  const handleResize = () => {
    const mobile = window.innerWidth < 768
    isMobile.value = mobile
    if (!mobile) {
      isMobileOpen.value = false
    }
  }

  onMounted(() => {
    handleResize()
    window.addEventListener('resize', handleResize)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
  })

  /** Alterna el sidebar (móvil: drawer; escritorio: expandir/colapsar). */
  const toggleSidebar = () => {
    if (isMobile.value) {
      isMobileOpen.value = !isMobileOpen.value
    } else {
      isExpanded.value = !isExpanded.value
    }
  }

  /** Alterna el drawer móvil. */
  const toggleMobileSidebar = () => {
    isMobileOpen.value = !isMobileOpen.value
  }

  /** Cierra el drawer móvil. */
  const closeMobileSidebar = () => {
    isMobileOpen.value = false
  }

  /** Define si el mouse está sobre el sidebar (modo colapsado). */
  const setIsHovered = (value: boolean) => {
    isHovered.value = value
  }

  /** Define el ítem activo del menú. */
  const setActiveItem = (item: string | null) => {
    activeItem.value = item
  }

  /** Abre/cierra el submenú indicado. */
  const toggleSubmenu = (item: string) => {
    openSubmenu.value = openSubmenu.value === item ? null : item
  }

  const context: SidebarContextType = {
    isExpanded: computed(() => (isMobile.value ? false : isExpanded.value)),
    isMobileOpen,
    isHovered,
    activeItem,
    openSubmenu,
    toggleSidebar,
    toggleMobileSidebar,
    closeMobileSidebar,
    setIsHovered,
    setActiveItem,
    toggleSubmenu,
  }

  provide(SidebarSymbol, context)

  return context
}

/**
 * Consume el contexto del sidebar dentro de un `SidebarProvider`.
 *
 * @throws Error si se usa fuera de un `SidebarProvider`.
 */
export function useSidebar(): SidebarContextType {
  const context = inject<SidebarContextType>(SidebarSymbol)
  if (!context) {
    throw new Error(
      'useSidebar must be used within a component that has SidebarProvider as an ancestor',
    )
  }
  return context
}
