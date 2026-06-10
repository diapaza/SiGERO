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
      class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0"
    >
      <div
        class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800"
      >
        <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">Notification</h5>
        <button class="text-gray-500 dark:text-gray-400" @click="closeDropdown">
          <CloseIcon class="fill-current" />
        </button>
      </div>

      <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
        <li v-for="notification in notifications" :key="notification.id" @click="handleItemClick">
          <a
            class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5"
            href="#"
          >
            <span class="relative block w-full h-10 rounded-full z-1 max-w-10">
              <img :src="notification.userImage" alt="User" class="overflow-hidden rounded-full" />
              <span
                :class="notification.status === 'online' ? 'bg-success-500' : 'bg-error-500'"
                class="absolute bottom-0 right-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"
              />
            </span>
            <span class="block">
              <span class="mb-1.5 block text-theme-sm text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-800 dark:text-white/90">
                  {{ notification.userName }}
                </span>
                {{ notification.action }}
                <span class="font-medium text-gray-800 dark:text-white/90">
                  {{ notification.project }}
                </span>
              </span>
              <span class="flex items-center gap-2 text-gray-500 text-theme-xs dark:text-gray-400">
                <span>{{ notification.type }}</span>
                <span class="w-1 h-1 bg-gray-400 rounded-full" />
                <span>{{ notification.time }}</span>
              </span>
            </span>
          </a>
        </li>
      </ul>

      <router-link
        to="#"
        class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
        @click="handleViewAllClick"
      >
        View All Notification
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import BellIcon from '@/icons/BellIcon.vue'
import CloseIcon from '@/icons/CloseIcon.vue'
import { useClickOutside } from '@/composables/useClickOutside'
import { notifications as notificationData } from '@/data/notifications'

const dropdownOpen = ref(false)
const notifying = ref(true)
const dropdownRef = ref<HTMLElement | null>(null)

const notifications = ref(notificationData)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
  notifying.value = false
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const handleItemClick = (event: Event) => {
  event.preventDefault()
  closeDropdown()
}

const handleViewAllClick = (event: Event) => {
  event.preventDefault()
  closeDropdown()
}

useClickOutside(dropdownRef, closeDropdown)
</script>
