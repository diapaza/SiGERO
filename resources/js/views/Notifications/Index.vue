<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="pageTitle" />

    <div class="space-y-6">
      <ComponentCard
        title="Notificaciones"
        desc="Historial de todas tus notificaciones del sistema."
      >
        <div
          v-if="notifications.data.length === 0"
          class="py-8 text-center text-gray-500 dark:text-gray-400"
        >
          No tienes notificaciones.
        </div>

        <ul v-else class="flex flex-col divide-y divide-gray-100 dark:divide-gray-800">
          <li
            v-for="notification in notifications.data"
            :key="notification.id"
            class="flex gap-3 p-3"
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
          </li>
        </ul>

        <div
          v-if="notifications.last_page > 1"
          class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800"
        >
          <span class="text-theme-sm text-gray-500 dark:text-gray-400">
            Página {{ notifications.current_page }} de {{ notifications.last_page }}
          </span>
          <div class="flex items-center gap-2">
            <button
              :disabled="!notifications.prev_page_url"
              class="px-3 py-1 text-sm rounded-md text-gray-600 hover:bg-gray-100 disabled:text-gray-300 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/[0.05] dark:disabled:text-gray-600"
              @click="goToPage(notifications.current_page - 1)"
            >
              ‹ Anterior
            </button>
            <button
              v-for="link in pageLinks"
              :key="link.label"
              :class="[
                'px-3 py-1 text-sm rounded-md min-w-[32px]',
                link.active
                  ? 'bg-brand-500 text-white'
                  : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.05]',
              ]"
              :disabled="link.url === null"
              @click="goToUrl(link.url)"
            >
              {{ link.label }}
            </button>
            <button
              :disabled="!notifications.next_page_url"
              class="px-3 py-1 text-sm rounded-md text-gray-600 hover:bg-gray-100 disabled:text-gray-300 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/[0.05] dark:disabled:text-gray-600"
              @click="goToPage(notifications.current_page + 1)"
            >
              Siguiente ›
            </button>
          </div>
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

/** * Página de todas las notificaciones del usuario. * * Vista renderizada por
`NotificationController@index`. Lista las * notificaciones paginadas (10 por página) con indicador
de leída/no leída y * tiempo relativo. * * Props Inertia: `notifications` (paginated con `data`,
`current_page`, * `last_page`, `links`, `prev_page_url`, `next_page_url`). */
<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import UserAvatar from '@/components/shared/UserAvatar.vue'
import type { Notification, Paginated } from '@/types/models'

const props = defineProps<{
  notifications: Paginated<Notification>
}>()

const pageTitle = ref('Notificaciones')

const pageLinks = computed(() => props.notifications.links ?? [])

const goToPage = (page: number) => {
  router.get(route('notifications.index', { page }))
}

const goToUrl = (url: string | null) => {
  if (url) {
    router.get(url)
  }
}

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
</script>
