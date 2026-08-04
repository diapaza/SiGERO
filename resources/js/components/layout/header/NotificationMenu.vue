<template>
  <div ref="dropdownRef" class="relative">
    <button
      class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
      @click="toggleDropdown"
    >
      <span
        :class="{ hidden: !notifying, flex: notifying }"
        class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400"
      >
        <span
          class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"
        />
      </span>
      <BellIcon class="fill-current" />
    </button>

    <div
      v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px]"
    >
      <div
        class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800"
      >
        <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Notificaciones</h5>
        <button class="text-gray-500 dark:text-gray-400" @click="closeDropdown">
          <CloseIcon class="fill-current" />
        </button>
      </div>

      <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
        <li v-for="notification in notifications" :key="notification.id" @click="handleItemClick">
          <a
            class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5"
            href="#"
            @click.prevent
          >
            <span class="relative block w-full h-10 rounded-full z-1 max-w-10">
              <UserAvatar :name="notification.title" :size="40" />
              <span
                :class="notification.read ? 'bg-gray-300' : 'bg-success-500'"
                class="absolute bottom-0 right-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"
              />
            </span>
            <span class="block">
              <span class="mb-1.5 block text-theme-sm text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-800 dark:text-white/90">
                  {{ notification.title }}
                </span>
              </span>
              <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
                {{ notification.message }}
              </span>
              <span
                class="flex items-center gap-2 mt-1 text-gray-500 text-theme-xs dark:text-gray-400"
              >
                <span>{{ typeLabel(notification.type) }}</span>
                <span class="w-1 h-1 bg-gray-400 rounded-full" />
                <span>{{ relativeTime(notification.created_at) }}</span>
              </span>
            </span>
          </a>
        </li>

        <li v-if="notifications.length === 0">
          <p class="py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400">
            No tienes notificaciones.
          </p>
        </li>
      </ul>

      <Link
        :href="route('notifications.index')"
        class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
      >
        Ver todas las notificaciones
      </Link>
    </div>
  </div>
</template>

/** * Menú desplegable de notificaciones de la cabecera. * * Consume las props compartidas de
Inertia `notifications` y `unreadNotifications` * (definidas en HandleInertiaRequests) para listar
las notificaciones del usuario * y marcar las no leídas como leídas al abrir el menú. Actualiza los
datos cada 30 * segundos mediante usePoll y se cierra al hacer clic fuera del contenedor. */
<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import BellIcon from '@/icons/BellIcon.vue'
import CloseIcon from '@/icons/CloseIcon.vue'
import UserAvatar from '@/components/shared/UserAvatar.vue'
import { useClickOutside } from '@/composables/useClickOutside'
import { usePoll } from '@inertiajs/vue3'
import type { Notification } from '@/types/models'

const dropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const page = usePage()

// Props compartidas por Inertia: lista de notificaciones y total de no leídas.
const notifications = computed<Notification[]>(() => (page.props as any).notifications ?? [])
const unreadCount = computed<number>(() => (page.props as any).unreadNotifications ?? 0)

/** Indica si existen notificaciones sin leer (muestra el indicador pulsante). */
const notifying = computed(() => unreadCount.value > 0)

// Consulta periódica (cada 30 s) de las notificaciones y su contador de no leídas.
usePoll(30000, {
  only: ['notifications', 'unreadNotifications'],
})

/**
 * Abre o cierra el desplegable y, al abrirlo, marca como leídas todas las
 * notificaciones pendientes a través del router de Inertia.
 */
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value

  if (dropdownOpen.value && unreadCount.value > 0) {
    router.post(
      route('notifications.read-all'),
      {},
      {
        preserveState: true,
        preserveScroll: true,
        only: ['notifications', 'unreadNotifications'],
      },
    )
  }
}

/** Cierra el desplegable de notificaciones. */
const closeDropdown = () => {
  dropdownOpen.value = false
}

/** Cierra el desplegable al seleccionar una notificación. */
const handleItemClick = () => {
  closeDropdown()
}

/** Devuelve la etiqueta legible en español para el tipo de notificación. */
const typeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    vencida: 'Vencida',
    salida: 'Salida',
    retorno: 'Retorno',
    permisos: 'Permisos',
    cuenta: 'Cuenta',
  }
  return labels[type] ?? 'Sistema'
}

/** Formatea la fecha de la notificación como tiempo relativo en español. */
const relativeTime = (dateString: string): string => {
  const date = new Date(dateString)
  const diffMs = Date.now() - date.getTime()
  const diffMin = Math.floor(diffMs / 60000)

  if (diffMin < 1) return 'Ahora'
  if (diffMin < 60) return `Hace ${diffMin} min`
  const diffHrs = Math.floor(diffMin / 60)
  if (diffHrs < 24) return `Hace ${diffHrs} h`
  const diffDays = Math.floor(diffHrs / 24)
  if (diffDays < 7) return `Hace ${diffDays} d`
  return date.toLocaleDateString('es-PE')
}

useClickOutside(dropdownRef, closeDropdown)
</script>
