<template>
  <div ref="dropdown" v-click-outside="closeDropdown" class="relative">
    <!-- Dropdown Trigger Button -->
    <button :class="buttonClass" @click="toggleDropdown">
      <slot name="icon">
        <!-- Default icon -->
        <HorizontalDots class="fill-current" />
      </slot>
    </button>

    <!-- Dropdown Menu -->
    <div v-if="open" :class="menuClass">
      <slot name="menu">
        <!-- Default menu items -->
        <template v-for="(item, index) in menuItems">
          <router-link
            v-if="item.to"
            :key="`router-${index}`"
            :to="item.to"
            :class="itemClass"
            @click="handleMenuItemClick(item.onClick)"
          >
            {{ item.label }}
          </router-link>

          <button
            v-else
            :key="`button-${index}`"
            :class="itemClass"
            @click="handleMenuItemClick(item.onClick)"
          >
            {{ item.label }}
          </button>
        </template>
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import vClickOutsideDirective from './directives/v-click-outside.vue'
import HorizontalDots from '@/icons/HorizontalDots.vue'

const vClickOutside = vClickOutsideDirective

interface MenuItem {
  label: string
  to?: string
  onClick?: () => void
}

const props = withDefaults(
  defineProps<{
    menuItems?: MenuItem[]
    buttonClass?: string
    menuClass?: string
    itemClass?: string
  }>(),
  {
    menuItems: () => [],
    buttonClass: 'text-gray-500 dark:text-gray-400',
    menuClass:
      'absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-lg dark:border-gray-800 dark:bg-gray-dark',
    itemClass:
      'flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300',
  },
)

const open = ref(false)

const toggleDropdown = () => {
  open.value = !open.value
}

const closeDropdown = () => {
  open.value = false
}

const handleMenuItemClick = (callback?: () => void) => {
  if (typeof callback === 'function') {
    callback()
  }
  closeDropdown()
}
</script>
