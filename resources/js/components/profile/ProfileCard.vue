<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <div
            class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800"
          >
            <img src="/images/user/owner.jpg" alt="user" />
          </div>
          <div class="order-3 xl:order-2">
            <h4
              class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left"
            >
              Musharof Chowdhury
            </h4>
            <div
              class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left"
            >
              <p class="text-sm text-gray-500 dark:text-gray-400">Team Manager</p>
              <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block" />
              <p class="text-sm text-gray-500 dark:text-gray-400">Arizona, United States</p>
            </div>
          </div>
          <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
            <a
              href="https://www.facebook.com/PimjoHQ"
              target="_blank"
              rel="noopener"
              class="social-button"
            >
              <FacebookIcon class="fill-current" />
            </a>
            <a href="https://x.com/PimjoHQ" target="_blank" rel="noopener" class="social-button">
              <XLogoIcon class="fill-current" />
            </a>
            <a
              href="https://www.linkedin.com/company/pimjo/"
              target="_blank"
              rel="noopener"
              class="social-button"
            >
              <LinkedInIcon class="fill-current" />
            </a>
          </div>
        </div>
        <button class="edit-button" @click="isProfileInfoModal = true">
          <EditIcon class="fill-current" />
          Edit
        </button>
      </div>
    </div>
    <ProfileEditModal
      v-model:is-open="isProfileInfoModal"
      :initial-data="profileData"
      @save="handleSave"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import FacebookIcon from '@/icons/FacebookIcon.vue'
import XLogoIcon from '@/icons/XLogoIcon.vue'
import LinkedInIcon from '@/icons/LinkedInIcon.vue'
import EditIcon from '@/icons/EditIcon.vue'
import ProfileEditModal from './ProfileEditModal.vue'

const isProfileInfoModal = ref(false)

const profileData = {
  firstName: 'Musharof',
  lastName: 'Chowdhury',
  email: 'randomuser@pimjo.com',
  phone: '+09 363 398 46',
  bio: 'Team Manager',
  facebook: 'https://www.facebook.com/PimjoHQ',
  twitter: 'https://x.com/PimjoHQ',
  linkedin: 'https://www.linkedin.com/company/pimjo/posts/?feedView=all',
  instagram: 'https://instagram.com/PimjoHQ',
}

const handleSave = (data: Record<string, string>) => {
  // TODO: Implement save logic
}
</script>
