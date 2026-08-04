<template>
  <div ref="dropdownRef" class="relative">
    <button
      class="flex items-center text-gray-700 dark:text-gray-400"
      @click.prevent="toggleDropdown"
    >
      <span class="mr-3 overflow-hidden rounded-full">
        <UserAvatar :name="userName" :size="44" />
      </span>

      <span class="block mr-1 font-medium text-theme-sm">{{ userName }}</span>

      <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
    >
      <div>
        <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">
          {{ userName }}
        </span>
        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
          {{ userUsername }}
        </span>
      </div>

      <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
        <li v-for="item in menuItems" :key="item.href">
          <Link
            :href="item.href"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
          >
            <component
              :is="item.icon"
              class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
            />
            {{ item.text }}
          </Link>
        </li>
      </ul>
      <form @submit.prevent="handleLogout">
        <button
          type="submit"
          class="flex items-center gap-3 px-3 py-2 mt-3 w-full font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
        >
          <LogoutIcon
            class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
          />
          Cerrar sesión
        </button>
      </form>
    </div>
    <!-- Dropdown End -->
  </div>
</template>

/** * Menú de usuario de la cabecera. * * Muestra el avatar y el nombre del usuario autenticado a
partir de las props * compartidas de Inertia. Despliega las opciones del perfil y el botón de cerrar
* sesión, y se cierra al hacer clic fuera del contenedor. */
<script setup lang="ts">
import { UserCircleIcon, ChevronDownIcon, LogoutIcon } from '@/icons'
import UserAvatar from '@/components/shared/UserAvatar.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useClickOutside } from '@/composables/useClickOutside'

const page = usePage<{ auth?: { user?: { name: string; username: string } | null } | null }>()

const userName = computed(() => page.props.auth?.user?.name ?? 'Usuario')
const userUsername = computed(() => page.props.auth?.user?.username ?? '')

const dropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const menuItems = [{ href: '/profile', icon: UserCircleIcon, text: 'Mi perfil' }]

/** Abre o cierra el menú desplegable de usuario. */
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

/** Cierra el menú desplegable de usuario. */
const closeDropdown = () => {
  dropdownOpen.value = false
}

/** Cierra el menú y cierra la sesión del usuario mediante el router de Inertia. */
const handleLogout = () => {
  closeDropdown()
  router.post(route('logout'))
}

useClickOutside(dropdownRef, closeDropdown)
</script>
