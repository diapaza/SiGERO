<template>
  <div>
    <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Address</h4>
          <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Country</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">United States</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">City/State</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">Phoenix, United States</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Postal Code</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">ERT 2489</p>
            </div>
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">TAX ID</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">AS4568384</p>
            </div>
          </div>
        </div>
        <button
          @click="isProfileAddressModal = true"
          class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto"
        >
          <EditIcon class="fill-current" />
          Edit
        </button>
      </div>
    </div>
    <BaseModal v-model:isOpen="isProfileAddressModal" title="Edit Address" size="lg" contentClass="rounded-3xl">
      <template #body>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Update your details to keep your profile up-to-date.</p>
        <div class="overflow-y-auto custom-scrollbar">
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div>
              <BaseLabel label="Country" />
              <BaseInput type="text" modelValue="United States" />
            </div>
            <div>
              <BaseLabel label="City/State" />
              <BaseInput type="text" modelValue="Poenix, Arizona, United States" />
            </div>
            <div>
              <BaseLabel label="Postal Code" />
              <BaseInput type="text" modelValue="ERT 2489" />
            </div>
            <div>
              <BaseLabel label="TAX ID" />
              <BaseInput type="text" modelValue="AS4568384" />
            </div>
          </div>
        </div>
      </template>
      <template #actions>
        <BaseButton variant="outline" @click="isProfileAddressModal = false">Close</BaseButton>
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

const isProfileAddressModal = ref(false)

const saveProfile = () => {
  console.log('Profile saved')
  isProfileAddressModal.value = false
}
</script>
