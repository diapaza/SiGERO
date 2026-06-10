import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUIStore = defineStore('ui', () => {
  const isSidebarExpanded = ref(true)
  const isMobileSidebarOpen = ref(false)
  const isAppLoading = ref(false)
  const notifications = ref<
    Array<{ id: number; message: string; type: 'success' | 'error' | 'warning' | 'info' }>
  >([])

  const setSidebarExpanded = (value: boolean) => {
    isSidebarExpanded.value = value
  }

  const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value
  }

  const closeMobileSidebar = () => {
    isMobileSidebarOpen.value = false
  }

  const setLoading = (value: boolean) => {
    isAppLoading.value = value
  }

  const addNotification = (notification: {
    message: string
    type: 'success' | 'error' | 'warning' | 'info'
  }) => {
    const id = Date.now()
    notifications.value.push({ id, ...notification })
    setTimeout(() => {
      removeNotification(id)
    }, 5000)
  }

  const removeNotification = (id: number) => {
    notifications.value = notifications.value.filter((n) => n.id !== id)
  }

  return {
    isSidebarExpanded,
    isMobileSidebarOpen,
    isAppLoading,
    notifications,
    setSidebarExpanded,
    toggleMobileSidebar,
    closeMobileSidebar,
    setLoading,
    addNotification,
    removeNotification,
  }
})
