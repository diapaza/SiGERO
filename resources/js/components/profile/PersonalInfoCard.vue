<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Personal Information</h4>
          <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">First Name</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">Musharof</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Last Name</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">Chowdhury</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Email address</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">randomuser@pimjo.com</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Phone</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">+09 363 398 46</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Bio</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">Team Manager</p>
            </div>
          </div>
        </div>
        <button class="edit-button" @click="isProfileInfoModal = true">
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
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Social Links</h5>
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
            <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Personal Information</h5>
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
                <BaseInput type="text" modelValue="emirhanboruch55@gmail.com" />
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
import EditIcon from '@/icons/EditIcon.vue'

const isProfileInfoModal = ref(false)

const saveProfile = () => {
  console.log('Profile saved')
  isProfileInfoModal.value = false
}
</script>
