<template>
  <div class="space-y-6">
    <!-- Email Input -->
    <div>
      <BaseLabel label="Email" />
      <BaseInput v-model="email" type="text" placeholder="info@gmail.com">
        <template #prepend>
          <MailIcon class="fill-current" />
        </template>
      </BaseInput>
    </div>

    <!-- Phone Input with Prepended Country Code -->
    <div>
      <BaseLabel label="Phone" />
      <div class="relative">
        <div class="absolute">
          <select
            v-model="selectedCountry"
            @change="updatePhoneNumber"
            class="appearance-none rounded-l-lg border-0 border-r border-gray-200 bg-transparent bg-none py-3 pl-3.5 pr-8 leading-tight text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:text-gray-400"
          >
            <option v-for="(code, country) in countryCodes" :key="country" :value="country">{{ country }}</option>
          </select>
          <div class="absolute inset-y-0 flex items-center text-gray-700 pointer-events-none right-3 dark:text-gray-400">
            <ChevronDownIcon class="stroke-current" />
          </div>
        </div>
        <input
          v-model="phoneNumber"
          placeholder="+1 (555) 000-0000"
          type="tel"
          class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-3 pl-[84px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />
      </div>
    </div>

    <!-- Phone Input with Appended Country Code -->
    <div>
      <BaseLabel label="Phone" />
      <div class="relative">
        <div class="absolute right-0">
          <select
            v-model="selectedCountry2"
            @change="updatePhoneNumber2"
            class="appearance-none rounded-r-lg border-0 border-l border-gray-200 bg-transparent bg-none py-3 pl-3.5 pr-8 leading-tight text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:text-gray-400"
          >
            <option v-for="(code, country) in countryCodes" :key="country" :value="country">{{ country }}</option>
          </select>
          <div class="absolute inset-y-0 flex items-center text-gray-700 pointer-events-none right-3 dark:text-gray-400">
            <ChevronDownIcon class="stroke-current" />
          </div>
        </div>
        <input
          v-model="phoneNumber2"
          placeholder="+1 (555) 000-0000"
          type="tel"
          class="dark:bg-dark-900 h-11 w-full p-3 rounded-lg border border-gray-300 bg-transparent py-3 pr-[84px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />
      </div>
    </div>

    <!-- URL Input -->
    <div>
      <BaseLabel label="URL" />
      <BaseInput v-model="url" type="url" placeholder="www.tailadmin.com">
        <template #prepend>
          <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">http://</span>
        </template>
      </BaseInput>
    </div>

    <!-- Website Input with Copy Button -->
    <div>
      <BaseLabel label="Website" />
      <div class="relative">
        <button
          @click="copyWebsite"
          class="absolute right-0 top-1/2 inline-flex -translate-y-1/2 cursor-pointer items-center gap-1 border-l border-gray-200 py-3 pl-3.5 pr-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:text-gray-400"
        >
          <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.58822 4.58398C6.58822 4.30784 6.81207 4.08398 7.08822 4.08398H15.4154C15.6915 4.08398 15.9154 4.30784 15.9154 4.58398L15.9154 12.9128C15.9154 13.189 15.6916 13.4128 15.4154 13.4128H7.08821C6.81207 13.4128 6.58822 13.189 6.58822 12.9128V4.58398ZM7.08822 2.58398C5.98365 2.58398 5.08822 3.47942 5.08822 4.58398V5.09416H4.58496C3.48039 5.09416 2.58496 5.98959 2.58496 7.09416V15.4161C2.58496 16.5207 3.48039 17.4161 4.58496 17.4161H12.9069C14.0115 17.4161 14.9069 16.5207 14.9069 15.4161L14.9069 14.9128H15.4154C16.52 14.9128 17.4154 14.0174 17.4154 12.9128L17.4154 4.58398C17.4154 3.47941 16.52 2.58398 15.4154 2.58398H7.08822ZM13.4069 14.9128H7.08821C5.98364 14.9128 5.08822 14.0174 5.08822 12.9128V6.59416H4.58496C4.30882 6.59416 4.08496 6.81801 4.08496 7.09416V15.4161C4.08496 15.6922 4.30882 15.9161 4.58496 15.9161H12.9069C13.183 15.9161 13.4069 15.6922 13.4069 15.4161L13.4069 14.9128Z" fill="" />
          </svg>
          <div>{{ copyText }}</div>
        </button>
        <input
          v-model="website"
          type="url"
          class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-3 pl-4 pr-[90px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import MailIcon from '@/icons/MailIcon.vue'
import ChevronDownIcon from '@/icons/ChevronDownIcon.vue'

const email = ref('')
const selectedCountry = ref('US')
const selectedCountry2 = ref('US')
const phoneNumber = ref('')
const phoneNumber2 = ref('')
const url = ref('')
const website = ref('www.tailadmin.com')
const copyText = ref('Copy')

const countryCodes = {
  US: '+1',
  GB: '+44',
  CA: '+1',
  AU: '+61',
}

const updatePhoneNumber = () => {
  phoneNumber.value = countryCodes[selectedCountry.value as keyof typeof countryCodes]
}

const updatePhoneNumber2 = () => {
  phoneNumber2.value = countryCodes[selectedCountry2.value as keyof typeof countryCodes]
}

const copyWebsite = () => {
  navigator.clipboard.writeText(website.value)
  copyText.value = 'Copied!'
  setTimeout(() => {
    copyText.value = 'Copy'
  }, 2000)
}
</script>
