<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
            <img src="/images/user/owner.jpg" alt="user" />
          </div>
          <div class="order-3 xl:order-2">
            <h4 class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
              Musharof Chowdhury
            </h4>
            <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
              <p class="text-sm text-gray-500 dark:text-gray-400">Team Manager</p>
              <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Arizona, United States</p>
            </div>
          </div>
          <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
            <a href="https://www.facebook.com/PimjoHQ" target="_blank" rel="noopener" class="social-button">
              <FacebookIcon class="fill-current" />
            </a>
            <a href="https://x.com/PimjoHQ" target="_blank" rel="noopener" class="social-button">
              <XLogoIcon class="fill-current" />
            </a>
            <a href="https://www.linkedin.com/company/pimjo/" target="_blank" rel="noopener" class="social-button">
              <LinkedInIcon class="fill-current" />
            </a>
          </div>
        </div>
        <button @click="isProfileInfoModal = true" class="edit-button">
          <EditIcon class="fill-current" />
          Edit
        </button>
      </div>
    </div>
    <BaseModal v-model:isOpen="isProfileInfoModal" title="Edit Personal Information" size="lg" contentClass="rounded-3xl">
      <template #body>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Update your details to keep your profile up-to-date.</p>
        <div class="custom-scrollbar h-[458px] overflow-y-auto">
          <div>
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
              Social Links
            </h5>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <div>
                <BaseLabel label="Facebook" />
                <BaseInput type="text" modelValue="https://www.facebook.com/PimjoHQ" />
              </div>
              <div>
                <BaseLabel label="X.com" />
                <BaseInput type="text" modelValue="https://x.com/PimjoHQ" />
              </div>
              <div>
                <BaseLabel label="Linkedin" />
                <BaseInput type="text" modelValue="https://www.linkedin.com/company/pimjo/posts/?feedView=all" />
              </div>
              <div>
                <BaseLabel label="Instagram" />
                <BaseInput type="text" modelValue="https://instagram.com/PimjoHQ" />
              </div>
            </div>
          </div>
          <div class="mt-7">
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
              Personal Information
            </h5>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <div class="col-span-2 lg:col-span-1">
                <BaseLabel label="First Name" />
                <BaseInput type="text" modelValue="Musharof" />
              </div>
              <div class="col-span-2 lg:col-span-1">
                <BaseLabel label="Last Name" />
                <BaseInput type="text" modelValue="Chowdhury" />
              </div>
              <div class="col-span-2 lg:col-span-1">
                <BaseLabel label="Email Address" />
                <BaseInput type="text" modelValue="randomuser@pimjo.com" />
              </div>
              <div class="col-span-2 lg:col-span-1">
                <BaseLabel label="Phone" />
                <BaseInput type="text" modelValue="+09 363 398 46" />
              </div>
              <div class="col-span-2">
                <BaseLabel label="Bio" />
                <BaseInput type="text" modelValue="Team Manager" />
              </div>
            </div>
          </div>
        </div>
      </template>
      <template #actions>
        <BaseButton variant="outline" @click="isProfileInfoModal = false">Close</BaseButton>
        <BaseButton variant="primary" @click="saveProfile">Save Changes</BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import FacebookIcon from '@/icons/FacebookIcon.vue'
import XLogoIcon from '@/icons/XLogoIcon.vue'
import LinkedInIcon from '@/icons/LinkedInIcon.vue'
import EditIcon from '@/icons/EditIcon.vue'

const isProfileInfoModal = ref(false)

const saveProfile = () => {
  console.log('Profile saved')
  isProfileInfoModal.value = false
}
</script>
